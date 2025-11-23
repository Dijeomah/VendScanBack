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
     * Get server's orders
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
                ->with(['orderItems.item', 'table', 'businessLink.business_data', 'payment'])
                ->orderBy('created_at', 'desc');

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

            $orders = $query->paginate($request->get('per_page', 15));

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

            Log::info('validated', ['data', $validated]);
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
