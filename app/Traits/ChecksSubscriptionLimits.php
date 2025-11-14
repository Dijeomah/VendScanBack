<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ChecksSubscriptionLimits
{
    /**
     * Check if user can create a resource
     *
     * @param string $resource (businesses, tables, servers, items)
     * @return JsonResponse|null Returns error response if limit reached, null if allowed
     */
    protected function checkLimit(string $resource): ?JsonResponse
    {
        $user = auth()->user();

        if (!$user->canCreate($resource)) {
            $remaining = $user->getRemainingSlots($resource);
            $plan = $user->subscriptionPlan;

            return error(
                "You've reached your {$resource} limit for the {$plan->name} plan. Upgrade to create more.",
                [
                    'resource' => $resource,
                    'current_plan' => $plan->slug,
                    'limit_reached' => true,
                    'upgrade_required' => true,
                    'available_plans' => $this->getUpgradePlans($plan),
                ],
                403
            );
        }

        return null;
    }

    /**
     * Get available upgrade plans
     */
    private function getUpgradePlans($currentPlan)
    {
        $plans = \App\Models\SubscriptionPlan::where('is_active', true)
            ->where('price', '>', $currentPlan->price)
            ->orderBy('price')
            ->get(['id', 'name', 'slug', 'price']);

        return $plans;
    }

    /**
     * Get current usage and limits for a resource
     */
    protected function getResourceUsage(string $resource): array
    {
        $user = auth()->user();
        $plan = $user->subscriptionPlan;

        $current = $user->getResourceCount($resource);
        $limit = $plan->getLimit($resource);
        $remaining = $user->getRemainingSlots($resource);

        return [
            'resource' => $resource,
            'current' => $current,
            'limit' => $limit ?? 'unlimited',
            'remaining' => $remaining,
            'can_create' => $user->canCreate($resource),
            'plan' => [
                'name' => $plan->name,
                'slug' => $plan->slug,
            ],
        ];
    }

    /**
     * Check multiple resources at once
     */
    protected function checkMultipleLimits(array $resources): ?JsonResponse
    {
        foreach ($resources as $resource) {
            $check = $this->checkLimit($resource);
            if ($check) {
                return $check;
            }
        }

        return null;
    }
}
