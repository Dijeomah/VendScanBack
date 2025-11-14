<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Http\Controllers\Controller;
use App\Mail\DisputeReportedMail;
use App\Mail\DisputeResolvedMail;
use App\Models\PaymentDispute;
use App\Models\SubscriptionPayment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AdminPaymentReconciliationController extends Controller
{
    private $paystackSecretKey;

    public function __construct()
    {
        $this->paystackSecretKey = config('services.paystack.secret_key');
    }

    /**
     * Get all unreconciled payments
     */
    public function getUnreconciledPayments(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);

            $payments = SubscriptionPayment::with(['user', 'subscription_plan', 'disputes'])
                ->unreconciled()
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return success('Unreconciled payments retrieved', [
                'payments' => $payments,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching unreconciled payments: ' . $e->getMessage());
            return error('Failed to fetch unreconciled payments', [], 500);
        }
    }

    /**
     * Get all disputed payments
     */
    public function getDisputedPayments(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);

            $payments = SubscriptionPayment::with(['user', 'subscription_plan', 'disputes.reportedBy', 'disputes.resolvedBy'])
                ->disputed()
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return success('Disputed payments retrieved', [
                'payments' => $payments,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching disputed payments: ' . $e->getMessage());
            return error('Failed to fetch disputed payments', [], 500);
        }
    }

    /**
     * Verify payment with Paystack
     */
    public function verifyWithPaystack(Request $request, $paymentId): JsonResponse
    {
        try {
            $payment = SubscriptionPayment::findOrFail($paymentId);

            if (!$payment->paystack_reference) {
                return error('Payment has no Paystack reference', [], 400);
            }

            // Verify with Paystack API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->paystackSecretKey,
                'Cache-Control' => 'no-cache',
            ])->get("https://api.paystack.co/transaction/verify/{$payment->paystack_reference}");

            if (!$response->successful()) {
                return error('Failed to verify payment with Paystack', [
                    'error' => $response->json(),
                ], 400);
            }

            $data = $response->json();

            if (!$data['status']) {
                return error('Paystack verification failed', ['data' => $data], 400);
            }

            $paystackData = $data['data'];

            // Check if payment matches
            $discrepancies = [];

            if ($paystackData['amount'] != ($payment->amount * 100)) {
                $discrepancies[] = [
                    'field' => 'amount',
                    'database' => $payment->amount,
                    'paystack' => $paystackData['amount'] / 100,
                ];
            }

            if ($paystackData['status'] !== 'success' && $payment->status === 'completed') {
                $discrepancies[] = [
                    'field' => 'status',
                    'database' => $payment->status,
                    'paystack' => $paystackData['status'],
                ];
            }

            $matchStatus = count($discrepancies) === 0 ? 'matched' : 'discrepancy_found';

            return success('Payment verified with Paystack', [
                'match_status' => $matchStatus,
                'discrepancies' => $discrepancies,
                'paystack_data' => $paystackData,
                'payment' => $payment,
            ]);
        } catch (\Exception $e) {
            Log::error('Error verifying payment with Paystack: ' . $e->getMessage());
            return error('Failed to verify payment', [], 500);
        }
    }

    /**
     * Reconcile a payment
     */
    public function reconcilePayment(Request $request, $paymentId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string|max:1000',
            'verify_with_paystack' => 'boolean',
        ]);

        if ($validator->fails()) {
            return error('Validation failed', ['errors' => $validator->errors()], 422);
        }

        try {
            $payment = SubscriptionPayment::findOrFail($paymentId);
            $admin = auth()->user();

            if ($payment->isReconciled()) {
                return error('Payment already reconciled', [], 400);
            }

            // Optionally verify with Paystack first
            if ($request->get('verify_with_paystack', false) && $payment->paystack_reference) {
                $verificationResponse = $this->verifyWithPaystack($request, $paymentId);
                $verificationData = $verificationResponse->getData(true);

                if ($verificationData['success'] && $verificationData['data']['match_status'] === 'discrepancy_found') {
                    return error('Cannot reconcile payment with discrepancies. Please review first.', [
                        'discrepancies' => $verificationData['data']['discrepancies'],
                    ], 400);
                }
            }

            DB::beginTransaction();

            $payment->markAsReconciled($admin->id, $request->notes);

            DB::commit();

            return success('Payment reconciled successfully', [
                'payment' => $payment->fresh(['reconciledBy']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error reconciling payment: ' . $e->getMessage());
            return error('Failed to reconcile payment', [], 500);
        }
    }

    /**
     * Bulk reconcile payments
     */
    public function bulkReconcile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'payment_ids' => 'required|array',
            'payment_ids.*' => 'exists:subscription_payments,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return error('Validation failed', ['errors' => $validator->errors()], 422);
        }

        try {
            $admin = auth()->user();
            $reconciled = [];
            $failed = [];

            DB::beginTransaction();

            foreach ($request->payment_ids as $paymentId) {
                try {
                    $payment = SubscriptionPayment::find($paymentId);

                    if ($payment && !$payment->isReconciled()) {
                        $payment->markAsReconciled($admin->id, $request->notes);
                        $reconciled[] = $paymentId;
                    } else {
                        $failed[] = [
                            'id' => $paymentId,
                            'reason' => 'Already reconciled or not found',
                        ];
                    }
                } catch (\Exception $e) {
                    $failed[] = [
                        'id' => $paymentId,
                        'reason' => $e->getMessage(),
                    ];
                }
            }

            DB::commit();

            return success('Bulk reconciliation completed', [
                'reconciled_count' => count($reconciled),
                'failed_count' => count($failed),
                'reconciled' => $reconciled,
                'failed' => $failed,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in bulk reconciliation: ' . $e->getMessage());
            return error('Failed to perform bulk reconciliation', [], 500);
        }
    }

    /**
     * Get reconciliation statistics
     */
    public function getReconciliationStats(): JsonResponse
    {
        try {
            $stats = [
                'total_payments' => SubscriptionPayment::count(),
                'reconciled' => SubscriptionPayment::reconciled()->count(),
                'unreconciled' => SubscriptionPayment::unreconciled()->count(),
                'disputed' => SubscriptionPayment::disputed()->count(),
                'pending_disputes' => PaymentDispute::pending()->count(),
                'investigating_disputes' => PaymentDispute::investigating()->count(),
                'resolved_disputes' => PaymentDispute::resolved()->count(),
                'total_unreconciled_amount' => SubscriptionPayment::unreconciled()
                    ->where('status', 'completed')
                    ->sum('amount'),
            ];

            return success('Reconciliation statistics retrieved', [
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching reconciliation stats: ' . $e->getMessage());
            return error('Failed to fetch statistics', [], 500);
        }
    }

    /**
     * Report a payment dispute
     */
    public function reportDispute(Request $request, $paymentId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'dispute_type' => 'required|in:non_receipt,duplicate,unauthorized,amount_mismatch,refund_request,other',
            'reason' => 'required|string|max:1000',
            'disputed_amount' => 'nullable|numeric|min:0',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return error('Validation failed', ['errors' => $validator->errors()], 422);
        }

        try {
            $payment = SubscriptionPayment::findOrFail($paymentId);
            $admin = auth()->user();

            DB::beginTransaction();

            $dispute = PaymentDispute::create([
                'subscription_payment_id' => $payment->id,
                'user_id' => $payment->user_id,
                'reported_by' => $admin->id,
                'dispute_type' => $request->dispute_type,
                'reason' => $request->reason,
                'disputed_amount' => $request->disputed_amount ?? $payment->amount,
                'metadata' => $request->metadata,
                'status' => 'pending',
            ]);

            // Mark payment as disputed
            $payment->update(['has_dispute' => true]);

            DB::commit();

            // Send email notification to user
            try {
                Mail::to($payment->user->email)->send(new DisputeReportedMail($payment->user, $dispute));
            } catch (\Exception $e) {
                Log::error('Failed to send dispute reported email: ' . $e->getMessage());
            }

            return success('Dispute reported successfully', [
                'dispute' => $dispute->load(['payment', 'user', 'reportedBy']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error reporting dispute: ' . $e->getMessage());
            return error('Failed to report dispute', [], 500);
        }
    }

    /**
     * Get all disputes
     */
    public function getAllDisputes(Request $request): JsonResponse
    {
        try {
            $status = $request->get('status');
            $perPage = $request->get('per_page', 15);

            $query = PaymentDispute::with([
                'payment.subscription_plan',
                'user',
                'reportedBy',
                'resolvedBy',
            ]);

            if ($status) {
                $query->where('status', $status);
            }

            $disputes = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return success('Disputes retrieved successfully', [
                'disputes' => $disputes,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching disputes: ' . $e->getMessage());
            return error('Failed to fetch disputes', [], 500);
        }
    }

    /**
     * Get dispute details
     */
    public function getDisputeDetails($disputeId): JsonResponse
    {
        try {
            $dispute = PaymentDispute::with([
                'payment.subscription_plan',
                'payment.user',
                'user',
                'reportedBy',
                'resolvedBy',
            ])->findOrFail($disputeId);

            return success('Dispute details retrieved', [
                'dispute' => $dispute,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching dispute details: ' . $e->getMessage());
            return error('Failed to fetch dispute details', [], 500);
        }
    }

    /**
     * Update dispute status
     */
    public function updateDisputeStatus(Request $request, $disputeId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:investigating,resolved,rejected',
            'resolution_notes' => 'required_if:status,resolved,rejected|string|max:1000',
            'refund_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return error('Validation failed', ['errors' => $validator->errors()], 422);
        }

        try {
            $dispute = PaymentDispute::findOrFail($disputeId);
            $admin = auth()->user();

            DB::beginTransaction();

            switch ($request->status) {
                case 'investigating':
                    $dispute->markAsInvestigating($admin->id);
                    break;
                case 'resolved':
                    $dispute->resolve($admin->id, $request->resolution_notes, $request->refund_amount);
                    break;
                case 'rejected':
                    $dispute->reject($admin->id, $request->resolution_notes);
                    break;
            }

            DB::commit();

            // Send email notification for resolved or rejected disputes
            if (in_array($request->status, ['resolved', 'rejected'])) {
                try {
                    $dispute->refresh();
                    Mail::to($dispute->user->email)->send(new DisputeResolvedMail($dispute->user, $dispute));
                } catch (\Exception $e) {
                    Log::error('Failed to send dispute resolved email: ' . $e->getMessage());
                }
            }

            return success('Dispute status updated successfully', [
                'dispute' => $dispute->fresh(['payment', 'user', 'reportedBy', 'resolvedBy']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating dispute status: ' . $e->getMessage());
            return error('Failed to update dispute status', [], 500);
        }
    }

    /**
     * Search for discrepancies between database and Paystack
     */
    public function searchDiscrepancies(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return error('Validation failed', ['errors' => $validator->errors()], 422);
        }

        try {
            $query = SubscriptionPayment::where('status', 'completed')
                ->whereNotNull('paystack_reference');

            if ($request->start_date) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->end_date) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            $payments = $query->get();
            $discrepancies = [];

            foreach ($payments as $payment) {
                try {
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $this->paystackSecretKey,
                    ])->get("https://api.paystack.co/transaction/verify/{$payment->paystack_reference}");

                    if ($response->successful()) {
                        $data = $response->json();
                        if ($data['status'] && isset($data['data'])) {
                            $paystackData = $data['data'];

                            $issues = [];
                            if ($paystackData['amount'] != ($payment->amount * 100)) {
                                $issues[] = 'amount_mismatch';
                            }

                            if ($paystackData['status'] !== 'success') {
                                $issues[] = 'status_mismatch';
                            }

                            if (!empty($issues)) {
                                $discrepancies[] = [
                                    'payment_id' => $payment->id,
                                    'reference' => $payment->reference,
                                    'issues' => $issues,
                                    'database_amount' => $payment->amount,
                                    'paystack_amount' => $paystackData['amount'] / 100,
                                    'database_status' => $payment->status,
                                    'paystack_status' => $paystackData['status'],
                                ];
                            }
                        }
                    }

                    // Add delay to avoid rate limiting
                    usleep(200000); // 200ms delay
                } catch (\Exception $e) {
                    Log::error("Error verifying payment {$payment->id}: " . $e->getMessage());
                }
            }

            return success('Discrepancy search completed', [
                'total_checked' => $payments->count(),
                'discrepancies_found' => count($discrepancies),
                'discrepancies' => $discrepancies,
            ]);
        } catch (\Exception $e) {
            Log::error('Error searching for discrepancies: ' . $e->getMessage());
            return error('Failed to search for discrepancies', [], 500);
        }
    }
}
