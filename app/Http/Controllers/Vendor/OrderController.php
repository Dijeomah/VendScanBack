<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Get all orders for vendor with advanced filtering, sorting, and pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $vendorId = authUser()->id;

            $query = Order::forVendor($vendorId)
                ->with(['orderItems.item', 'table', 'businessLink', 'server', 'payment']);

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

            // Filter by business
            if ($request->filled('business_link_id')) {
                $query->where('business_link_id', $request->business_link_id);
            }

            // Filter by table
            if ($request->filled('table_id')) {
                $query->where('table_id', $request->table_id);
            }

            // Filter by server
            if ($request->filled('server_id')) {
                $query->where('server_id', $request->server_id);
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
            Log::error('Vendor orders fetch error: ' . $e->getMessage());
            return error('Error fetching orders', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single order
     */
    public function show(int $id): JsonResponse
    {
        try {
            $vendorId = authUser()->id;

            $order = Order::forVendor($vendorId)
                ->with(['orderItems.item.category', 'table', 'businessLink', 'server', 'payment'])
                ->findOrFail($id);

            return success('Order fetched successfully', $order, Response::HTTP_OK);
        } catch (\Exception $e) {
            return error('Order not found', null, Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,confirmed,preparing,served,completed,cancelled',
            ]);

            $vendorId = authUser()->id;

            $order = Order::forVendor($vendorId)->findOrFail($id);
            $order->status = $validated['status'];
            $order->save();

            $order->load(['orderItems.item', 'table', 'businessLink', 'server', 'payment']);

            return success('Order status updated successfully', $order, Response::HTTP_OK);
        } catch (\Exception $e) {
            return error('Error updating order status', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'payment_status' => 'required|in:pending,paid,failed',
            ]);

            $vendorId = authUser()->id;

            $order = Order::forVendor($vendorId)->findOrFail($id);
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

            $order->load(['orderItems.item', 'table', 'businessLink', 'server', 'payment']);

            return success('Payment status updated successfully', $order, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error updating payment status: ' . $e->getMessage());
            return error('Error updating payment status', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get enhanced order statistics for vendor
     */
    public function getStatistics(Request $request): JsonResponse
    {
        try {
            $vendorId = authUser()->id;

            // Filter by business if provided
            $businessId = $request->get('business_link_id');

            $query = Order::forVendor($vendorId);
            if ($businessId) {
                $query->where('business_link_id', $businessId);
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

            // Top servers by sales
            $topServers = (clone $query)
                ->select('server_id', DB::raw('count(*) as order_count'), DB::raw('sum(total) as total_sales'))
                ->where('payment_status', 'paid')
                ->whereNotNull('server_id')
                ->groupBy('server_id')
                ->with('server:id,first_name,last_name,email')
                ->orderBy('total_sales', 'desc')
                ->limit(5)
                ->get();

            // Top tables by orders
            $topTables = (clone $query)
                ->select('table_id', DB::raw('count(*) as order_count'), DB::raw('sum(total) as total_sales'))
                ->where('payment_status', 'paid')
                ->whereNotNull('table_id')
                ->groupBy('table_id')
                ->with('table:id,table_number,table_name')
                ->orderBy('order_count', 'desc')
                ->limit(5)
                ->get();

            // Last 7 days revenue
            $revenueByDay = (clone $query)
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('sum(total) as revenue'), DB::raw('count(*) as orders'))
                ->where('payment_status', 'paid')
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('date', 'desc')
                ->get();

            // Top selling items
            $topItems = OrderItem::whereHas('order', function ($q) use ($vendorId, $businessId) {
                    $q->forVendor($vendorId)->where('payment_status', 'paid');
                    if ($businessId) {
                        $q->where('business_link_id', $businessId);
                    }
                })
                ->select('item_id', DB::raw('sum(quantity) as total_quantity'), DB::raw('sum(subtotal) as total_revenue'))
                ->groupBy('item_id')
                ->with('item:id,title,price,category_id')
                ->orderBy('total_revenue', 'desc')
                ->limit(10)
                ->get();

            // Recent orders
            $recentOrders = (clone $query)
                ->with(['table:id,table_number', 'server:id,first_name,last_name'])
                ->latest()
                ->limit(5)
                ->get(['id', 'order_number', 'total', 'status', 'payment_status', 'table_id', 'server_id', 'created_at']);

            return success('Statistics fetched successfully', [
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
                'top_servers' => $topServers,
                'top_tables' => $topTables,
                'revenue_by_day' => $revenueByDay,
                'top_items' => $topItems,
                'recent_orders' => $recentOrders,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Statistics fetch error: ' . $e->getMessage());
            return error('Error fetching statistics', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
