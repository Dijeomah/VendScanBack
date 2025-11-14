<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPayment;
use App\Mail\PaymentConfirmedMail;
use App\Mail\SubscriptionUpgradedMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    protected $paystackSecretKey;
    protected $paystackPublicKey;

    public function __construct()
    {
        $this->paystackSecretKey = config('services.paystack.secret_key');
        $this->paystackPublicKey = config('services.paystack.public_key');
    }

    /**
     * Initialize a payment for subscription upgrade
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function initializePayment(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'plan_id' => 'required|exists:subscription_plans,id',
            ]);

            $user = Auth::user();
            $plan = SubscriptionPlan::findOrFail($validated['plan_id']);

            // Don't allow payment for free plan
            if ($plan->isFree()) {
                return error('Cannot purchase free plan', null, Response::HTTP_BAD_REQUEST);
            }

            // Check if user is already on this plan
            $currentPlan = $user->subscriptionPlan ?? SubscriptionPlan::where('slug', 'free')->first();
            if ($currentPlan->id === $plan->id) {
                return error('You are already on this plan', null, Response::HTTP_BAD_REQUEST);
            }

            // Create payment record
            $payment = SubscriptionPayment::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'amount' => $plan->price,
                'currency' => 'NGN',
                'status' => 'pending',
                'payment_method' => 'paystack',
            ]);

            // Initialize Paystack payment
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->paystackSecretKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.paystack.co/transaction/initialize', [
                'email' => $user->email,
                'amount' => $plan->price * 100, // Convert to kobo (Paystack uses smallest currency unit)
                'currency' => 'NGN',
                'reference' => $payment->reference,
                'callback_url' => config('app.frontend_url') . '/subscription/verify',
                'metadata' => [
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'plan_name' => $plan->name,
                    'payment_id' => $payment->id,
                ],
            ]);

            if (!$response->successful()) {
                Log::error('Paystack initialization failed', [
                    'response' => $response->body(),
                    'status' => $response->status(),
                ]);

                $payment->update(['status' => 'failed']);

                return error('Failed to initialize payment', [
                    'error' => $response->json()['message'] ?? 'Unknown error'
                ], Response::HTTP_BAD_REQUEST);
            }

            $data = $response->json();

            // Update payment with Paystack reference
            $payment->update([
                'paystack_reference' => $data['data']['reference'],
                'paystack_access_code' => $data['data']['access_code'],
            ]);

            Log::info('Payment initialized', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'reference' => $payment->reference,
            ]);

            return success('Payment initialized successfully', [
                'authorization_url' => $data['data']['authorization_url'],
                'access_code' => $data['data']['access_code'],
                'reference' => $payment->reference,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Payment initialization error: ' . $e->getMessage());
            return error('Failed to initialize payment', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Verify payment after user completes payment
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyPayment(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'reference' => 'required|string',
            ]);

            $reference = $validated['reference'];

            // Verify payment with Paystack
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->paystackSecretKey,
            ])->get("https://api.paystack.co/transaction/verify/{$reference}");

            if (!$response->successful()) {
                return error('Failed to verify payment', null, Response::HTTP_BAD_REQUEST);
            }

            $data = $response->json();

            if ($data['data']['status'] !== 'success') {
                return error('Payment was not successful', [
                    'status' => $data['data']['status']
                ], Response::HTTP_BAD_REQUEST);
            }

            // Find payment record
            $payment = SubscriptionPayment::where('reference', $reference)
                ->orWhere('paystack_reference', $reference)
                ->firstOrFail();

            // Check if payment was already processed
            if ($payment->status === 'completed') {
                return success('Payment already processed', [
                    'payment' => $payment,
                ], Response::HTTP_OK);
            }

            // Update payment status
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
                'paystack_data' => $data['data'],
            ]);

            // Upgrade user's subscription
            $user = $payment->user;
            $newPlan = $payment->subscription_plan;
            $oldPlan = $user->subscriptionPlan;

            // Cancel old subscription if exists
            $oldSubscription = $user->subscription;
            if ($oldSubscription) {
                $oldSubscription->cancel();
            }

            // Update user's plan
            $user->update([
                'subscription_plan_id' => $newPlan->id,
            ]);

            // Create new subscription
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $newPlan->id,
                'status' => 'active',
                'started_at' => now(),
                'expires_at' => now()->addMonth(), // 1 month subscription
            ]);

            // Send confirmation emails
            try {
                Mail::to($user->email)->send(new PaymentConfirmedMail($user, $payment));
                Mail::to($user->email)->send(new SubscriptionUpgradedMail($user, $newPlan, $oldPlan));
            } catch (\Exception $e) {
                Log::error('Failed to send payment emails: ' . $e->getMessage());
                // Don't fail the payment process if email fails
            }

            Log::info('Subscription upgraded via payment', [
                'user_id' => $user->id,
                'old_plan' => $oldPlan->slug ?? 'none',
                'new_plan' => $newPlan->slug,
                'payment_id' => $payment->id,
            ]);

            return success('Payment verified and subscription upgraded successfully', [
                'payment' => $payment,
                'subscription' => $subscription->load('plan'),
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Payment verification error: ' . $e->getMessage());
            return error('Failed to verify payment', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Handle Paystack webhooks
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        try {
            // Verify webhook signature
            $signature = $request->header('x-paystack-signature');
            $body = $request->getContent();

            if (!$signature || $signature !== hash_hmac('sha512', $body, $this->paystackSecretKey)) {
                Log::warning('Invalid webhook signature');
                return response()->json(['error' => 'Invalid signature'], 400);
            }

            $event = $request->all();
            $eventType = $event['event'];

            Log::info('Paystack webhook received', [
                'event' => $eventType,
                'data' => $event['data'] ?? null,
            ]);

            switch ($eventType) {
                case 'charge.success':
                    $this->handleChargeSuccess($event['data']);
                    break;

                case 'subscription.create':
                case 'subscription.not_renew':
                case 'subscription.disable':
                    $this->handleSubscriptionEvent($event['data'], $eventType);
                    break;

                default:
                    Log::info('Unhandled webhook event: ' . $eventType);
            }

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error('Webhook handling error: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Handle successful charge webhook
     *
     * @param array $data
     * @return void
     */
    protected function handleChargeSuccess(array $data): void
    {
        try {
            $reference = $data['reference'];

            $payment = SubscriptionPayment::where('paystack_reference', $reference)
                ->orWhere('reference', $reference)
                ->first();

            if (!$payment || $payment->status === 'completed') {
                return;
            }

            // Update payment
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
                'paystack_data' => $data,
            ]);

            // Upgrade subscription (same logic as verify)
            $user = $payment->user;
            $newPlan = $payment->subscription_plan;
            $oldPlan = $user->subscriptionPlan;

            $oldSubscription = $user->subscription;
            if ($oldSubscription) {
                $oldSubscription->cancel();
            }

            $user->update(['subscription_plan_id' => $newPlan->id]);

            Subscription::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $newPlan->id,
                'status' => 'active',
                'started_at' => now(),
                'expires_at' => now()->addMonth(),
            ]);

            // Send confirmation emails
            try {
                Mail::to($user->email)->send(new PaymentConfirmedMail($user, $payment));
                Mail::to($user->email)->send(new SubscriptionUpgradedMail($user, $newPlan, $oldPlan));
            } catch (\Exception $e) {
                Log::error('Failed to send webhook emails: ' . $e->getMessage());
            }

            Log::info('Subscription upgraded via webhook', [
                'user_id' => $user->id,
                'plan_id' => $newPlan->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Charge success webhook error: ' . $e->getMessage());
        }
    }

    /**
     * Handle subscription-related webhooks
     *
     * @param array $data
     * @param string $eventType
     * @return void
     */
    protected function handleSubscriptionEvent(array $data, string $eventType): void
    {
        try {
            // Log subscription events for monitoring
            Log::info('Subscription event received', [
                'event' => $eventType,
                'data' => $data,
            ]);

            // Add custom logic here based on your needs
            // For example, handle subscription renewal, cancellation, etc.

        } catch (\Exception $e) {
            Log::error('Subscription webhook error: ' . $e->getMessage());
        }
    }
}
