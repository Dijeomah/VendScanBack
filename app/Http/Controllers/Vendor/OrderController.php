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
     * Get all orders for vendor
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $vendorId = authUser()->id;

            $query = Order::forVendor($vendorId)
                ->with(['orderItems.item', 'table', 'businessLink', 'server', 'payment'])
                ->orderBy('created_at', 'desc');

            // Filter by status
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // Filter by payment status
            if ($request->has('payment_status') && $request->payment_status !== 'all') {
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
            if ($request->has('business_link_id')) {
                $query->where('business_link_id', $request->business_link_id);
            }

            $orders = $query->paginate($request->get('per_page', 15));

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
     * Get order statistics
     */
    public function getStatistics(Request $request): JsonResponse
    {
        try {
            $vendorId = authUser()->id;
            Log::info('Getting statistics for vendor: ' . $vendorId);

            // Debug: Check what SQL is being generated
            $query = Order::forVendor($vendorId);
            Log::info('SQL Query: ' . $query->toSql());
            Log::info('Query bindings: ' . json_encode($query->getBindings()));

            // Total orders
            $totalOrders = $query->count();
            Log::info('Total orders count: ' . $totalOrders);

            // Orders by status
            $ordersByStatus = Order::forVendor($vendorId)
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get();

            // Total revenue
            $totalRevenue = Order::forVendor($vendorId)
                ->where('payment_status', 'paid')
                ->sum('total');

            // Today's orders
            $todayOrders = Order::forVendor($vendorId)
                ->whereDate('created_at', today())
                ->count();

            // Today's revenue
            $todayRevenue = Order::forVendor($vendorId)
                ->whereDate('created_at', today())
                ->where('payment_status', 'paid')
                ->sum('total');

            // Top servers by sales
            $topServers = Order::forVendor($vendorId)
                ->select('server_id', DB::raw('count(*) as order_count'), DB::raw('sum(total) as total_sales'))
                ->where('payment_status', 'paid')
                ->whereNotNull('server_id')
                ->groupBy('server_id')
                ->with('server')
                ->orderBy('total_sales', 'desc')
                ->limit(5)
                ->get();

            // Last 7 days revenue
            $revenueByDay = Order::forVendor($vendorId)
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('sum(total) as revenue'), DB::raw('count(*) as orders'))
                ->where('payment_status', 'paid')
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('date', 'desc')
                ->get();

            // Top selling items
            $topItems = OrderItem::whereHas('order', function ($query) use ($vendorId) {
                    $query->forVendor($vendorId)->where('payment_status', 'paid');
                })
                ->select('item_id', DB::raw('sum(quantity) as total_quantity'), DB::raw('sum(subtotal) as total_revenue'))
                ->groupBy('item_id')
                ->with('item.category')
                ->orderBy('total_revenue', 'desc')
                ->limit(10)
                ->get();

            $statistics = [
                'total_orders' => $totalOrders,
                'orders_by_status' => $ordersByStatus,
                'total_revenue' => $totalRevenue,
                'today_orders' => $todayOrders,
                'today_revenue' => $todayRevenue,
                'top_servers' => $topServers,
                'revenue_by_day' => $revenueByDay,
                'top_items' => $topItems,
            ];

            Log::info('Statistics data:', $statistics);

            return success('Statistics fetched successfully', $statistics, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Statistics fetch error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return error('Error fetching statistics', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
