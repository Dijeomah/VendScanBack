<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPayment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AdminPaymentController extends Controller
{
    /**
     * Get all payments with filters
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);

            $payments = SubscriptionPayment::with(['user', 'subscription_plan'])
                ->when($request->get('status'), function ($query, $status) {
                    return $query->where('status', $status);
                })
                ->when($request->get('user_id'), function ($query, $userId) {
                    return $query->where('user_id', $userId);
                })
                ->when($request->get('plan_id'), function ($query, $planId) {
                    return $query->where('subscription_plan_id', $planId);
                })
                ->when($request->get('date_from'), function ($query, $dateFrom) {
                    return $query->whereDate('created_at', '>=', $dateFrom);
                })
                ->when($request->get('date_to'), function ($query, $dateTo) {
                    return $query->whereDate('created_at', '<=', $dateTo);
                })
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            // Get statistics
            $stats = [
                'total_payments' => SubscriptionPayment::count(),
                'completed_payments' => SubscriptionPayment::where('status', 'completed')->count(),
                'pending_payments' => SubscriptionPayment::where('status', 'pending')->count(),
                'failed_payments' => SubscriptionPayment::where('status', 'failed')->count(),
                'total_amount' => SubscriptionPayment::where('status', 'completed')->sum('amount'),
                'monthly_amount' => SubscriptionPayment::where('status', 'completed')
                    ->whereMonth('paid_at', now()->month)
                    ->whereYear('paid_at', now()->year)
                    ->sum('amount'),
            ];

            return success('Payments fetched successfully', [
                'payments' => $payments,
                'stats' => $stats,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching payments: ' . $e->getMessage());
            return error('Failed to fetch payments', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get payment details
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $payment = SubscriptionPayment::with(['user', 'subscription_plan'])
                ->findOrFail($id);

            return success('Payment details fetched successfully', $payment, Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching payment details: ' . $e->getMessage());
            return error('Payment not found', null, Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Get payment statistics for dashboard
     *
     * @return JsonResponse
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $today = now()->startOfDay();
            $thisWeek = now()->startOfWeek();
            $thisMonth = now()->startOfMonth();

            $stats = [
                // Revenue
                'revenue' => [
                    'today' => SubscriptionPayment::where('status', 'completed')
                        ->whereDate('paid_at', $today)
                        ->sum('amount'),
                    'this_week' => SubscriptionPayment::where('status', 'completed')
                        ->whereDate('paid_at', '>=', $thisWeek)
                        ->sum('amount'),
                    'this_month' => SubscriptionPayment::where('status', 'completed')
                        ->whereDate('paid_at', '>=', $thisMonth)
                        ->sum('amount'),
                    'all_time' => SubscriptionPayment::where('status', 'completed')
                        ->sum('amount'),
                ],

                // Payment counts
                'payments' => [
                    'today' => SubscriptionPayment::whereDate('created_at', $today)->count(),
                    'this_week' => SubscriptionPayment::whereDate('created_at', '>=', $thisWeek)->count(),
                    'this_month' => SubscriptionPayment::whereDate('created_at', '>=', $thisMonth)->count(),
                ],

                // Success rate
                'success_rate' => [
                    'today' => $this->calculateSuccessRate($today, 'day'),
                    'this_week' => $this->calculateSuccessRate($thisWeek, 'week'),
                    'this_month' => $this->calculateSuccessRate($thisMonth, 'month'),
                ],

                // Recent transactions
                'recent_transactions' => SubscriptionPayment::with(['user', 'subscription_plan'])
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get(),

                // Revenue by plan
                'revenue_by_plan' => SubscriptionPayment::where('status', 'completed')
                    ->selectRaw('subscription_plan_id, SUM(amount) as total')
                    ->groupBy('subscription_plan_id')
                    ->with('subscription_plan')
                    ->get(),

                // Daily revenue for last 30 days
                'daily_revenue' => $this->getDailyRevenue(30),
            ];

            return success('Payment statistics fetched successfully', $stats, Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error fetching payment statistics: ' . $e->getMessage());
            return error('Failed to fetch statistics', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Manually mark payment as completed (admin override)
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function markAsCompleted(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'reason' => 'required|string|max:500',
            ]);

            $payment = SubscriptionPayment::findOrFail($id);

            if ($payment->isCompleted()) {
                return error('Payment is already completed', null, Response::HTTP_BAD_REQUEST);
            }

            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
            ]);

            Log::info('Admin manually marked payment as completed', [
                'payment_id' => $payment->id,
                'user_id' => $payment->user_id,
                'admin_id' => auth()->id(),
                'reason' => $validated['reason'],
            ]);

            return success('Payment marked as completed successfully', $payment->fresh(), Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error marking payment as completed: ' . $e->getMessage());
            return error('Failed to update payment', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Issue refund for a payment
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function refund(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'reason' => 'required|string|max:500',
            ]);

            $payment = SubscriptionPayment::findOrFail($id);

            if (!$payment->isCompleted()) {
                return error('Can only refund completed payments', null, Response::HTTP_BAD_REQUEST);
            }

            $payment->update([
                'status' => 'refunded',
            ]);

            // Downgrade user to free plan
            $user = $payment->user;
            $freePlan = \App\Models\SubscriptionPlan::where('slug', 'free')->first();

            $user->update([
                'subscription_plan_id' => $freePlan->id,
            ]);

            // Cancel active subscription
            $activeSubscription = $user->subscription;
            if ($activeSubscription) {
                $activeSubscription->cancel();
            }

            Log::info('Admin issued refund', [
                'payment_id' => $payment->id,
                'user_id' => $payment->user_id,
                'amount' => $payment->amount,
                'admin_id' => auth()->id(),
                'reason' => $validated['reason'],
            ]);

            return success('Refund issued successfully. User downgraded to free plan.', $payment->fresh(), Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Error issuing refund: ' . $e->getMessage());
            return error('Failed to issue refund', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Calculate success rate for a given period
     *
     * @param \Carbon\Carbon $from
     * @param string $period
     * @return float
     */
    protected function calculateSuccessRate($from, string $period): float
    {
        $total = SubscriptionPayment::whereDate('created_at', '>=', $from)->count();

        if ($total === 0) {
            return 0;
        }

        $completed = SubscriptionPayment::where('status', 'completed')
            ->whereDate('created_at', '>=', $from)
            ->count();

        return round(($completed / $total) * 100, 2);
    }

    /**
     * Get daily revenue for last N days
     *
     * @param int $days
     * @return array
     */
    protected function getDailyRevenue(int $days): array
    {
        $revenue = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');

            $revenue[] = [
                'date' => $date,
                'amount' => SubscriptionPayment::where('status', 'completed')
                    ->whereDate('paid_at', $date)
                    ->sum('amount'),
            ];
        }

        return $revenue;
    }
}
