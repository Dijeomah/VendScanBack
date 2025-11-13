<?php

namespace App\Http\Controllers\Admin;

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
     * Get all orders
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Order::with(['orderItems.item', 'table', 'businessLink', 'server', 'vendor', 'payment'])
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

            // Filter by vendor
            if ($request->has('vendor_id')) {
                $query->where('vendor_id', $request->vendor_id);
            }

            $orders = $query->paginate($request->get('per_page', 15));

            return success('Orders fetched successfully', $orders, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Admin orders fetch error: ' . $e->getMessage());
            return error('Error fetching orders', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single order
     */
    public function show(int $id): JsonResponse
    {
        try {
            $order = Order::with(['orderItems.item.category', 'table', 'businessLink', 'server', 'vendor', 'payment'])
                ->findOrFail($id);

            return success('Order fetched successfully', $order, Response::HTTP_OK);
        } catch (\Exception $e) {
            return error('Order not found', null, Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Get platform-wide statistics
     */
    public function getStatistics(Request $request): JsonResponse
    {
        try {
            // Total orders
            $totalOrders = Order::count();

            // Orders by status
            $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get();

            // Total revenue
            $totalRevenue = Order::where('payment_status', 'paid')->sum('total');

            // Today's orders
            $todayOrders = Order::whereDate('created_at', today())->count();

            // Today's revenue
            $todayRevenue = Order::whereDate('created_at', today())
                ->where('payment_status', 'paid')
                ->sum('total');

            // Top vendors by revenue
            $topVendors = Order::select('vendor_id', DB::raw('count(*) as order_count'), DB::raw('sum(total) as total_sales'))
                ->where('payment_status', 'paid')
                ->groupBy('vendor_id')
                ->with('vendor')
                ->orderBy('total_sales', 'desc')
                ->limit(10)
                ->get();

            // Last 30 days revenue
            $revenueByDay = Order::select(DB::raw('DATE(created_at) as date'), DB::raw('sum(total) as revenue'), DB::raw('count(*) as orders'))
                ->where('payment_status', 'paid')
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->get();

            // Top selling items across platform
            $topItems = OrderItem::whereHas('order', function ($query) {
                    $query->where('payment_status', 'paid');
                })
                ->select('item_id', DB::raw('sum(quantity) as total_quantity'), DB::raw('sum(subtotal) as total_revenue'))
                ->groupBy('item_id')
                ->with('item.category')
                ->orderBy('total_revenue', 'desc')
                ->limit(10)
                ->get();

            // Revenue by payment method
            $revenueByPaymentMethod = Order::join('payments', 'orders.id', '=', 'payments.order_id')
                ->select('payments.payment_method', DB::raw('sum(orders.total) as revenue'), DB::raw('count(*) as count'))
                ->where('orders.payment_status', 'paid')
                ->groupBy('payments.payment_method')
                ->get();

            return success('Statistics fetched successfully', [
                'total_orders' => $totalOrders,
                'orders_by_status' => $ordersByStatus,
                'total_revenue' => $totalRevenue,
                'today_orders' => $todayOrders,
                'today_revenue' => $todayRevenue,
                'top_vendors' => $topVendors,
                'revenue_by_day' => $revenueByDay,
                'top_items' => $topItems,
                'revenue_by_payment_method' => $revenueByPaymentMethod,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Admin statistics fetch error: ' . $e->getMessage());
            return error('Error fetching statistics', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
