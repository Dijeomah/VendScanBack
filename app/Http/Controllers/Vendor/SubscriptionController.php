<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionController extends Controller
{
    /**
     * Get current subscription and usage statistics
     *
     * @return JsonResponse
     */
    public function getCurrentSubscription(): JsonResponse
    {
        try {
            $user = Auth::user();

            // Get subscription plan
            $plan = $user->subscriptionPlan ?? SubscriptionPlan::where('slug', 'free')->first();

            // Get active subscription record
            $subscription = $user->subscription;

            // Get resource usage
            $usage = [
                'businesses' => [
                    'current' => $user->getResourceCount('businesses'),
                    'limit' => $plan->getLimit('businesses'),
                    'remaining' => $user->getRemainingSlots('businesses'),
                    'unlimited' => $plan->isUnlimited('businesses')
                ],
                'tables' => [
                    'current' => $user->getResourceCount('tables'),
                    'limit' => $plan->getLimit('tables'),
                    'remaining' => $user->getRemainingSlots('tables'),
                    'unlimited' => $plan->isUnlimited('tables')
                ],
                'servers' => [
                    'current' => $user->getResourceCount('servers'),
                    'limit' => $plan->getLimit('servers'),
                    'remaining' => $user->getRemainingSlots('servers'),
                    'unlimited' => $plan->isUnlimited('servers')
                ],
                'items' => [
                    'current' => $user->getResourceCount('items'),
                    'limit' => $plan->getLimit('items'),
                    'remaining' => $user->getRemainingSlots('items'),
                    'unlimited' => $plan->isUnlimited('items')
                ],
            ];

            // Get available features
            $features = [
                'analytics' => $plan->has_analytics,
                'custom_branding' => $plan->has_custom_branding,
                'priority_support' => $plan->has_priority_support,
                'api_access' => $plan->has_api_access,
            ];

            return success('Current subscription fetched successfully', [
                'plan' => $plan,
                'subscription' => $subscription,
                'usage' => $usage,
                'features' => $features,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching subscription: ' . $e->getMessage());
            return error('Failed to fetch subscription', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get all available subscription plans
     *
     * @return JsonResponse
     */
    public function getPlans(): JsonResponse
    {
        try {
            $plans = SubscriptionPlan::where('is_active', true)
                ->orderBy('price', 'asc')
                ->get();

            $user = Auth::user();
            $currentPlan = $user->subscriptionPlan ?? SubscriptionPlan::where('slug', 'free')->first();

            // Mark which plans are available for upgrade
            $plans->each(function ($plan) use ($currentPlan) {
                $plan->is_current = $plan->id === $currentPlan->id;
                $plan->is_upgrade = $plan->price > $currentPlan->price;
                $plan->is_downgrade = $plan->price < $currentPlan->price;
            });

            return success('Subscription plans fetched successfully', $plans, Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching plans: ' . $e->getMessage());
            return error('Failed to fetch plans', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Cancel subscription (only for paid plans)
     *
     * @return JsonResponse
     */
    public function cancelSubscription(): JsonResponse
    {
        try {
            $user = Auth::user();
            $subscription = $user->subscription;

            if (!$subscription) {
                return error('No active subscription found', null, Response::HTTP_NOT_FOUND);
            }

            $plan = $subscription->plan;

            // Don't allow canceling free plan
            if ($plan->isFree()) {
                return error('Cannot cancel free plan', null, Response::HTTP_BAD_REQUEST);
            }

            // Cancel the subscription
            $subscription->cancel();

            // Downgrade to free plan
            $freePlan = SubscriptionPlan::where('slug', 'free')->first();
            $user->update(['subscription_plan_id' => $freePlan->id]);

            // Create new free subscription
            Subscription::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $freePlan->id,
                'status' => 'active',
                'started_at' => now(),
                'expires_at' => null,
            ]);

            Log::info('Subscription cancelled', [
                'user_id' => $user->id,
                'old_plan' => $plan->slug,
                'new_plan' => 'free',
            ]);

            return success('Subscription cancelled successfully. You have been moved to the free plan.', [
                'new_plan' => $freePlan,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error cancelling subscription: ' . $e->getMessage());
            return error('Failed to cancel subscription', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get subscription history
     *
     * @return JsonResponse
     */
    public function getSubscriptionHistory(): JsonResponse
    {
        try {
            $user = Auth::user();

            $subscriptions = Subscription::where('user_id', $user->id)
                ->with('plan')
                ->orderBy('created_at', 'desc')
                ->get();

            return success('Subscription history fetched successfully', $subscriptions, Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching subscription history: ' . $e->getMessage());
            return error('Failed to fetch subscription history', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
