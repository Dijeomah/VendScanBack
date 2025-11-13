<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BusinessLink;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Item;
use App\Models\TableLinkQrData;
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

            // Get table and assigned server
            $table = null;
            $serverId = null;
            if (!empty($validated['table_id'])) {
                $table = TableLinkQrData::with('server_assignments')->find($validated['table_id']);
                if ($table && $table->serverAssignment) {
                    $serverId = $table->serverAssignment->server_id;
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
}
