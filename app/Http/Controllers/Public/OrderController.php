<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BusinessLink;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Item;
use App\Models\TableLinkQrData;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Create a new order
     */
    public function createOrder(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'business_link' => 'required|string|exists:business_links,business_link',
                'table_id' => 'nullable|exists:table_link_qr_data,id',
                'customer_name' => 'nullable|string|max:255',
                'customer_phone' => 'nullable|string|max:20',
                'customer_latitude' => 'nullable|numeric|between:-90,90',
                'customer_longitude' => 'nullable|numeric|between:-180,180',
                'items' => 'required|array|min:1',
                'items.*.item_id' => 'required|exists:items,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.notes' => 'nullable|string',
                'notes' => 'nullable|string',
                'payment_method' => 'required|in:pay_before,pay_after',
            ]);

            DB::beginTransaction();

            // Get business
            $business = BusinessLink::where('business_link', $validated['business_link'])->firstOrFail();

            // Geofence validation
            if ($business->geofence_enabled) {
                if (empty($validated['customer_latitude']) || empty($validated['customer_longitude'])) {
                    DB::rollBack();
                    return error('Location permission is required to place an order at this business', null, Response::HTTP_FORBIDDEN);
                }

                if (empty($business->latitude) || empty($business->longitude)) {
                    DB::rollBack();
                    Log::warning('Business geofence enabled but location not set: ' . $business->id);
                    return error('Business location not configured. Please contact the business.', null, Response::HTTP_BAD_REQUEST);
                }

                // Calculate distance using Haversine formula
                $distance = $this->calculateDistance(
                    $validated['customer_latitude'],
                    $validated['customer_longitude'],
                    $business->latitude,
                    $business->longitude
                );

                Log::info('Geofence check', [
                    'business' => $business->business_name,
                    'customer_location' => [$validated['customer_latitude'], $validated['customer_longitude']],
                    'business_location' => [$business->latitude, $business->longitude],
                    'distance' => $distance,
                    'radius' => $business->geofence_radius
                ]);

                if ($distance > $business->geofence_radius) {
                    DB::rollBack();
                    return error(
                        "You must be within {$business->geofence_radius} meters of the business to place an order. You are currently " . round($distance) . " meters away.",
                        ['distance' => round($distance), 'required_radius' => $business->geofence_radius],
                        Response::HTTP_FORBIDDEN
                    );
                }
            }

            // Get table and assigned server
            $table = null;
            $serverId = null;
            if (!empty($validated['table_id'])) {
                $table = TableLinkQrData::with(['server_assignments' => function($query) {
                    $query->where('status', 'active')->with('server');
                }])->find($validated['table_id']);

                // Get the first active server assignment for this table
                if ($table && $table->server_assignments->isNotEmpty()) {
                    $serverId = $table->server_assignments->first()->server_id;
                }
            }

            // Calculate totals
            $subtotal = 0;
            $orderItemsData = [];

            foreach ($validated['items'] as $itemData) {
                $item = Item::findOrFail($itemData['item_id']);

                // Verify item belongs to the business
                if ($item->business_link !== $validated['business_link']) {
                    throw new \Exception('Item does not belong to this business');
                }

                $itemSubtotal = $item->price * $itemData['quantity'];
                $subtotal += $itemSubtotal;

                $orderItemsData[] = [
                    'item_id' => $item->id,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $item->price,
                    'subtotal' => $itemSubtotal,
                    'notes' => $itemData['notes'] ?? null,
                ];
            }

            $tax = $subtotal * 0.1; // 10% tax
            $total = $subtotal + $tax;

            // Create order
            $order = Order::create([
                'business_link_id' => $business->id,
                'table_id' => $validated['table_id'] ?? null,
                'server_id' => $serverId,
                'vendor_id' => $business->uid,
                'customer_name' => $validated['customer_name'] ?? null,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'notes' => $validated['notes'] ?? null,
                'payment_method' => $validated['payment_method'],
                'payment_status' => $validated['payment_method'] === 'pay_before' ? 'pending' : 'pending',
                'status' => 'pending',
            ]);

            // Create order items
            foreach ($orderItemsData as $itemData) {
                $order->orderItems()->create($itemData);
            }

            // If pay_before, create pending payment
            if ($validated['payment_method'] === 'pay_before') {
                Payment::create([
                    'order_id' => $order->id,
                    'amount' => $total,
                    'payment_method' => 'simulated',
                    'payment_status' => 'pending',
                ]);
            }

            DB::commit();

            // Load relationships
            $order->load(['orderItems.item', 'table', 'businessLink', 'payment']);

            // Send notifications
            try {
                // Notify vendor
                $vendor = User::find($business->uid);
                if ($vendor) {
                    $vendor->notify(new NewOrderNotification($order, 'vendor'));
                }

                // Notify assigned server if exists
                if ($serverId) {
                    $server = User::find($serverId);
                    if ($server) {
                        $server->notify(new NewOrderNotification($order, 'server'));
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to send order notifications: ' . $e->getMessage());
                // Don't fail the order if notification fails
            }

            return success('Order created successfully', $order, Response::HTTP_CREATED);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return error('Validation error', $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation error: ' . $e->getMessage());
            return error('Error creating order: ' . $e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Process payment
     */
    public function processPayment(Request $request, $orderId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'payment_method' => 'required|in:cash,card,mobile,simulated',
            ]);

            $order = Order::with('payment')->findOrFail((int)$orderId);

            if ($order->payment_status === 'paid') {
                return error('Order already paid', null, Response::HTTP_BAD_REQUEST);
            }

            DB::beginTransaction();

            // Create or update payment
            $payment = $order->payment;
            if (!$payment) {
                $payment = new Payment();
                $payment->order_id = $order->id;
                $payment->amount = $order->total;
            }

            // Simulate payment success
            $payment->payment_method = $validated['payment_method'];
            $payment->payment_status = 'completed';
            $payment->payment_at = now();
            $payment->payment_details = [
                'simulated' => true,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ];
            $payment->save();

            // Update order
            $order->payment_status = 'paid';
            $order->payment_at = now();
            if ($order->status === 'pending') {
                $order->status = 'confirmed';
            }
            $order->save();

            DB::commit();

            $order->load(['orderItems.item', 'table', 'businessLink', 'payment']);

            return success('Payment processed successfully', $order, Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing error: ' . $e->getMessage());
            return error('Error processing payment', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get order details
     */
    public function getOrder(string $orderNumber): JsonResponse
    {
        try {
            $order = Order::where('order_number', $orderNumber)
                ->with(['orderItems.item.category', 'table', 'businessLink', 'payment', 'server'])
                ->firstOrFail();

            return success('Order fetched successfully', $order, Response::HTTP_OK);
        } catch (\Exception $e) {
            return error('Order not found', null, Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     * Returns distance in meters
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371000; // Earth's radius in meters

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)
        ));

        return $angle * $earthRadius;
    }
}
