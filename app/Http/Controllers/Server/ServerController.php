<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ServerTableAssignment;
use App\Models\BusinessServer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServerController extends Controller
{
    /**
     * Get server dashboard statistics
     */
    public function getDashboardStatistics(): JsonResponse
    {
        try {
            $server = Auth::user();

            // Get assigned businesses
            $assignedBusinesses = BusinessServer::where('server_id', $server->id)
                ->where('status', 'active')
                ->with('business.business_data')
                ->get();

            // Get assigned tables
            $assignedTables = ServerTableAssignment::where('server_id', $server->id)
                ->where('status', 'active')
                ->with(['table', 'business.business_data'])
                ->get();

            // Get orders count for server's tables
            $tableIds = $assignedTables->pluck('table_id');
            $totalOrders = Order::whereIn('table_id', $tableIds)->count();

            // Today's orders
            $todayOrders = Order::whereIn('table_id', $tableIds)
                ->whereDate('created_at', today())
                ->count();

            // Pending orders count
            $pendingOrders = Order::whereIn('table_id', $tableIds)
                ->whereIn('status', ['pending', 'confirmed'])
                ->count();

            // Orders by status
            $ordersByStatus = Order::whereIn('table_id', $tableIds)
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get();

            // Recent orders (last 10)
            $recentOrders = Order::whereIn('table_id', $tableIds)
                ->with(['orderItems.item', 'table', 'businessLink.business_data', 'payment'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return success('Dashboard statistics fetched successfully', [
                'assigned_businesses' => $assignedBusinesses,
                'assigned_tables' => $assignedTables,
                'total_orders' => $totalOrders,
                'today_orders' => $todayOrders,
                'pending_orders' => $pendingOrders,
                'orders_by_status' => $ordersByStatus,
                'recent_orders' => $recentOrders,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Server dashboard statistics error: ' . $e->getMessage());
            return error('Error fetching dashboard statistics', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get server's assigned tables
     */
    public function getAssignedTables(): JsonResponse
    {
        try {
            $server = Auth::user();

            $tables = ServerTableAssignment::where('server_id', $server->id)
                ->where('status', 'active')
                ->with(['table', 'business.business_data'])
                ->get();

            return success('Assigned tables fetched successfully', $tables, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error fetching assigned tables: ' . $e->getMessage());
            return error('Error fetching assigned tables', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get server's assigned businesses
     */
    public function getAssignedBusinesses(): JsonResponse
    {
        try {
            $server = Auth::user();

            $businesses = BusinessServer::where('server_id', $server->id)
                ->where('status', 'active')
                ->with('business.business_data')
                ->get();

            return success('Assigned businesses fetched successfully', $businesses, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error fetching assigned businesses: ' . $e->getMessage());
            return error('Error fetching assigned businesses', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get server's orders with advanced filtering, sorting, and pagination
     */
    public function getOrders(Request $request): JsonResponse
    {
        try {
            $server = Auth::user();

            // Get server's assigned table IDs
            $tableIds = ServerTableAssignment::where('server_id', $server->id)
                ->where('status', 'active')
                ->pluck('table_id');

            $query = Order::whereIn('table_id', $tableIds)
                ->with(['orderItems.item', 'table', 'businessLink.business_data', 'payment']);

            // Search functionality
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('order_number', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('customer_name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('customer_phone', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('notes', 'LIKE', "%{$searchTerm}%");
                });
            }

            // Filter by status
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // Filter by payment status
            if ($request->filled('payment_status') && $request->payment_status !== 'all') {
                $query->where('payment_status', $request->payment_status);
            }

            // Filter by date range
            if ($request->filled('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }

            // Filter by specific table
            if ($request->filled('table_id')) {
                $query->where('table_id', $request->table_id);
            }

            // Filter by business
            if ($request->filled('business_link_id')) {
                $query->where('business_link_id', $request->business_link_id);
            }

            // Filter by total amount range
            if ($request->filled('min_total')) {
                $query->where('total', '>=', $request->min_total);
            }
            if ($request->filled('max_total')) {
                $query->where('total', '<=', $request->max_total);
            }

            // Filter by order type
            if ($request->filled('order_type')) {
                $query->where('order_type', $request->order_type);
            }

            // Sorting
            $sortField = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            $allowedSortFields = ['order_number', 'total', 'status', 'payment_status', 'created_at', 'updated_at'];
            if (in_array($sortField, $allowedSortFields)) {
                $query->orderBy($sortField, $sortOrder);
            } else {
                $query->orderBy('created_at', 'desc');
            }

            $perPage = $request->get('per_page', 15);
            $orders = $query->paginate($perPage);

            return success('Orders fetched successfully', $orders, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Server orders fetch error: ' . $e->getMessage());
            return error('Error fetching orders', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single order
     */
    public function getOrder($orderId): JsonResponse
    {
        try {
            $server = Auth::user();

            // Get server's assigned table IDs
            $tableIds = ServerTableAssignment::where('server_id', $server->id)
                ->where('status', 'active')
                ->pluck('table_id');

            $order = Order::whereIn('table_id', $tableIds)
                ->with(['orderItems.item.category', 'table', 'businessLink.business_data', 'payment'])
                ->findOrFail($orderId);

            return success('Order fetched successfully', $order, Response::HTTP_OK);
        } catch (\Exception $e) {
            return error('Order not found', null, Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(Request $request, $orderId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,confirmed,preparing,served,completed,cancelled',
            ]);

            $server = Auth::user();

            // Get server's assigned table IDs
            $tableIds = ServerTableAssignment::where('server_id', $server->id)
                ->where('status', 'active')
                ->pluck('table_id');

            $order = Order::whereIn('table_id', $tableIds)->findOrFail($orderId);
            $order->status = $validated['status'];
            $order->save();

            $order->load(['orderItems.item', 'table', 'businessLink.business_data', 'payment']);

            return success('Order status updated successfully', $order, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error updating order status: ' . $e->getMessage());
            return error('Error updating order status', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, $orderId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'payment_status' => 'required|in:pending,paid,failed',
            ]);

            $server = Auth::user();

            // Get server's assigned table IDs
            $tableIds = ServerTableAssignment::where('server_id', $server->id)
                ->where('status', 'active')
                ->pluck('table_id');

            $order = Order::whereIn('table_id', $tableIds)->findOrFail($orderId);
            $order->payment_status = $validated['payment_status'];

            // Update payment timestamp if marked as paid
            if ($validated['payment_status'] === 'paid' && !$order->payment_at) {
                $order->payment_at = now();
            }

            $order->save();

            // Also update related payment record if exists
            if ($order->payment) {
                $order->payment->payment_status = $validated['payment_status'] === 'paid' ? 'completed' : $validated['payment_status'];
                if ($validated['payment_status'] === 'paid' && !$order->payment->payment_at) {
                    $order->payment->payment_at = now();
                }
                $order->payment->save();
            }

            $order->load(['orderItems.item', 'table', 'businessLink.business_data', 'payment']);

            return success('Payment status updated successfully', $order, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error updating payment status: ' . $e->getMessage());
            return error('Error updating payment status', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get enhanced order statistics for server
     */
    public function getOrderStatistics(Request $request): JsonResponse
    {
        try {
            $server = Auth::user();

            // Get server's assigned table IDs
            $tableIds = ServerTableAssignment::where('server_id', $server->id)
                ->where('status', 'active')
                ->pluck('table_id');

            $query = Order::whereIn('table_id', $tableIds);

            // Filter by business if provided
            if ($request->filled('business_link_id')) {
                $query->where('business_link_id', $request->business_link_id);
            }

            // Total orders
            $totalOrders = (clone $query)->count();

            // Orders by status
            $ordersByStatus = (clone $query)
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get();

            // Orders by payment status
            $ordersByPaymentStatus = (clone $query)
                ->select('payment_status', DB::raw('count(*) as count'))
                ->groupBy('payment_status')
                ->get();

            // Total revenue (paid orders only)
            $totalRevenue = (clone $query)
                ->where('payment_status', 'paid')
                ->sum('total');

            // Pending revenue
            $pendingRevenue = (clone $query)
                ->where('payment_status', 'pending')
                ->sum('total');

            // Today's orders
            $todayOrders = (clone $query)
                ->whereDate('created_at', today())
                ->count();

            // Today's revenue
            $todayRevenue = (clone $query)
                ->whereDate('created_at', today())
                ->where('payment_status', 'paid')
                ->sum('total');

            // This week's orders
            $weekOrders = (clone $query)
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count();

            // This week's revenue
            $weekRevenue = (clone $query)
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->where('payment_status', 'paid')
                ->sum('total');

            // This month's orders
            $monthOrders = (clone $query)
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->count();

            // This month's revenue
            $monthRevenue = (clone $query)
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->where('payment_status', 'paid')
                ->sum('total');

            // Average order value
            $averageOrderValue = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0;

            // Top tables by orders
            $topTables = (clone $query)
                ->select('table_id', DB::raw('count(*) as order_count'), DB::raw('sum(total) as total_sales'))
                ->where('payment_status', 'paid')
                ->groupBy('table_id')
                ->with('table:id,table_number,table_name')
                ->orderBy('order_count', 'desc')
                ->limit(5)
                ->get();

            // Last 7 days orders
            $ordersByDay = (clone $query)
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('sum(total) as revenue'), DB::raw('count(*) as orders'))
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('date', 'desc')
                ->get();

            // Recent orders
            $recentOrders = (clone $query)
                ->with(['table:id,table_number', 'businessLink:id,business_link'])
                ->latest()
                ->limit(5)
                ->get(['id', 'order_number', 'total', 'status', 'payment_status', 'table_id', 'business_link_id', 'created_at']);

            return success('Order statistics fetched successfully', [
                'total_orders' => $totalOrders,
                'orders_by_status' => $ordersByStatus,
                'orders_by_payment_status' => $ordersByPaymentStatus,
                'total_revenue' => $totalRevenue,
                'pending_revenue' => $pendingRevenue,
                'average_order_value' => $averageOrderValue,
                'today_orders' => $todayOrders,
                'today_revenue' => $todayRevenue,
                'week_orders' => $weekOrders,
                'week_revenue' => $weekRevenue,
                'month_orders' => $monthOrders,
                'month_revenue' => $monthRevenue,
                'top_tables' => $topTables,
                'orders_by_day' => $ordersByDay,
                'recent_orders' => $recentOrders,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Server statistics fetch error: ' . $e->getMessage());
            return error('Error fetching statistics', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get server profile
     */
    public function getProfile(): JsonResponse
    {
        try {
            $server = Auth::user();
            $server->load(['assigned_businesses.business.business_data', 'assigned_tables.table', 'assigned_tables.business.business_data']);

            return success('Profile fetched successfully', $server, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error fetching server profile: ' . $e->getMessage());
            return error('Error fetching profile', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
