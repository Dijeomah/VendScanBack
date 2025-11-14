<?php

namespace App\Http\Controllers\Admin\Subscription;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPayment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AdminSubscriptionController extends Controller
{
    /**
     * Get all subscriptions with statistics
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);

            $subscriptions = Subscription::with(['user', 'plan'])
                ->when($request->get('status'), function ($query, $status) {
                    return $query->where('status', $status);
                })
                ->when($request->get('plan_id'), function ($query, $planId) {
                    return $query->where('subscription_plan_id', $planId);
                })
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            // Get statistics
            $stats = [
                'total_subscriptions' => Subscription::count(),
                'active_subscriptions' => Subscription::where('status', 'active')->count(),
                'cancelled_subscriptions' => Subscription::where('status', 'cancelled')->count(),
                'expired_subscriptions' => Subscription::where('status', 'expired')->count(),
                'trial_subscriptions' => Subscription::where('status', 'trial')->count(),
                'by_plan' => SubscriptionPlan::withCount('subscriptions')->get(),
            ];

            return success('Subscriptions fetched successfully', [
                'subscriptions' => $subscriptions,
                'stats' => $stats,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching subscriptions: ' . $e->getMessage());
            return error('Failed to fetch subscriptions', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get subscription details
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $subscription = Subscription::with(['user', 'plan'])
                ->findOrFail($id);

            // Get payment history for this subscription
            $payments = SubscriptionPayment::where('user_id', $subscription->user_id)
                ->where('subscription_plan_id', $subscription->subscription_plan_id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Get user resource usage
            $user = $subscription->user;
            $usage = [
                'businesses' => $user->getResourceCount('businesses'),
                'tables' => $user->getResourceCount('tables'),
                'servers' => $user->getResourceCount('servers'),
                'items' => $user->getResourceCount('items'),
            ];

            return success('Subscription details fetched successfully', [
                'subscription' => $subscription,
                'payments' => $payments,
                'usage' => $usage,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching subscription details: ' . $e->getMessage());
            return error('Subscription not found', null, Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update subscription status (admin override)
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:active,cancelled,expired,trial',
                'reason' => 'nullable|string|max:500',
            ]);

            $subscription = Subscription::findOrFail($id);
            $oldStatus = $subscription->status;

            $subscription->update([
                'status' => $validated['status'],
            ]);

            Log::info('Admin updated subscription status', [
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
                'old_status' => $oldStatus,
                'new_status' => $validated['status'],
                'admin_id' => auth()->id(),
                'reason' => $validated['reason'] ?? null,
            ]);

            return success('Subscription status updated successfully', $subscription->fresh(), Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error updating subscription status: ' . $e->getMessage());
            return error('Failed to update subscription', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Manually upgrade a user's subscription (admin override)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function manualUpgrade(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'plan_id' => 'required|exists:subscription_plans,id',
                'duration_months' => 'nullable|integer|min:1|max:12',
                'reason' => 'nullable|string|max:500',
            ]);

            $user = User::findOrFail($validated['user_id']);
            $newPlan = SubscriptionPlan::findOrFail($validated['plan_id']);
            $durationMonths = $validated['duration_months'] ?? 1;

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
                'expires_at' => $newPlan->isFree() ? null : now()->addMonths($durationMonths),
            ]);

            Log::info('Admin manually upgraded subscription', [
                'user_id' => $user->id,
                'new_plan_id' => $newPlan->id,
                'duration_months' => $durationMonths,
                'admin_id' => auth()->id(),
                'reason' => $validated['reason'] ?? null,
            ]);

            return success('Subscription upgraded successfully', $subscription->load('plan'), Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error manually upgrading subscription: ' . $e->getMessage());
            return error('Failed to upgrade subscription', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get subscription revenue statistics
     *
     * @return JsonResponse
     */
    public function getRevenueStats(): JsonResponse
    {
        try {
            $stats = [
                'total_revenue' => SubscriptionPayment::where('status', 'completed')->sum('amount'),
                'monthly_revenue' => SubscriptionPayment::where('status', 'completed')
                    ->whereMonth('paid_at', now()->month)
                    ->whereYear('paid_at', now()->year)
                    ->sum('amount'),
                'today_revenue' => SubscriptionPayment::where('status', 'completed')
                    ->whereDate('paid_at', today())
                    ->sum('amount'),
                'revenue_by_plan' => SubscriptionPlan::withCount([
                    'subscriptions as active_count' => function ($query) {
                        $query->where('status', 'active');
                    }
                ])->get()->map(function ($plan) {
                    $mrr = $plan->active_count * $plan->price;
                    return [
                        'plan' => $plan->name,
                        'price' => $plan->price,
                        'active_subscriptions' => $plan->active_count,
                        'monthly_recurring_revenue' => $mrr,
                    ];
                }),
                'recent_payments' => SubscriptionPayment::with(['user', 'subscription_plan'])
                    ->where('status', 'completed')
                    ->orderBy('paid_at', 'desc')
                    ->limit(10)
                    ->get(),
            ];

            return success('Revenue statistics fetched successfully', $stats, Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching revenue stats: ' . $e->getMessage());
            return error('Failed to fetch statistics', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get subscription plans management
     *
     * @return JsonResponse
     */
    public function getPlans(): JsonResponse
    {
        try {
            $plans = SubscriptionPlan::withCount([
                'subscriptions as active_count' => function ($query) {
                    $query->where('status', 'active');
                }
            ])->get();

            return success('Subscription plans fetched successfully', $plans, Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching plans: ' . $e->getMessage());
            return error('Failed to fetch plans', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update subscription plan
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updatePlan(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:100',
                'price' => 'sometimes|required|numeric|min:0',
                'max_businesses' => 'nullable|integer|min:1',
                'max_tables' => 'nullable|integer|min:1',
                'max_servers' => 'nullable|integer|min:1',
                'max_items' => 'nullable|integer|min:1',
                'has_analytics' => 'sometimes|boolean',
                'has_custom_branding' => 'sometimes|boolean',
                'has_priority_support' => 'sometimes|boolean',
                'has_api_access' => 'sometimes|boolean',
                'is_active' => 'sometimes|boolean',
            ]);

            $plan = SubscriptionPlan::findOrFail($id);
            $plan->update($validated);

            Log::info('Admin updated subscription plan', [
                'plan_id' => $plan->id,
                'changes' => $validated,
                'admin_id' => auth()->id(),
            ]);

            return success('Subscription plan updated successfully', $plan->fresh(), Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error updating plan: ' . $e->getMessage());
            return error('Failed to update plan', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
