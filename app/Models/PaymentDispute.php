<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentDispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_payment_id',
        'user_id',
        'reported_by',
        'resolved_by',
        'status',
        'dispute_type',
        'reason',
        'resolution_notes',
        'disputed_amount',
        'refund_amount',
        'reported_at',
        'resolved_at',
        'metadata',
    ];

    protected $casts = [
        'disputed_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'reported_at' => 'datetime',
        'resolved_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the payment associated with this dispute
     */
    public function payment()
    {
        return $this->belongsTo(SubscriptionPayment::class, 'subscription_payment_id');
    }

    /**
     * Get the user who made the payment
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the admin who reported the dispute
     */
    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /**
     * Get the admin who resolved the dispute
     */
    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Check if dispute is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if dispute is investigating
     */
    public function isInvestigating(): bool
    {
        return $this->status === 'investigating';
    }

    /**
     * Check if dispute is resolved
     */
    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    /**
     * Check if dispute is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Mark dispute as investigating
     */
    public function markAsInvestigating(int $adminId): void
    {
        $this->update([
            'status' => 'investigating',
            'reported_by' => $adminId,
        ]);
    }

    /**
     * Resolve the dispute
     */
    public function resolve(int $adminId, string $notes, ?float $refundAmount = null): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_by' => $adminId,
            'resolution_notes' => $notes,
            'refund_amount' => $refundAmount,
            'resolved_at' => now(),
        ]);

        // Update payment dispute flag
        $this->payment->update(['has_dispute' => false]);
    }

    /**
     * Reject the dispute
     */
    public function reject(int $adminId, string $notes): void
    {
        $this->update([
            'status' => 'rejected',
            'resolved_by' => $adminId,
            'resolution_notes' => $notes,
            'resolved_at' => now(),
        ]);

        // Update payment dispute flag
        $this->payment->update(['has_dispute' => false]);
    }

    /**
     * Scope for pending disputes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for investigating disputes
     */
    public function scopeInvestigating($query)
    {
        return $query->where('status', 'investigating');
    }

    /**
     * Scope for resolved disputes
     */
    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    /**
     * Scope for unresolved disputes (pending or investigating)
     */
    public function scopeUnresolved($query)
    {
        return $query->whereIn('status', ['pending', 'investigating']);
    }
}
