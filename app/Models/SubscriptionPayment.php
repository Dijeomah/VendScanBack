<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SubscriptionPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'reference',
        'paystack_reference',
        'paystack_access_code',
        'paystack_data',
        'paid_at',
        'is_reconciled',
        'reconciled_by',
        'reconciled_at',
        'reconciliation_notes',
        'has_dispute',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'paystack_data' => 'array',
        'is_reconciled' => 'boolean',
        'reconciled_at' => 'datetime',
        'has_dispute' => 'boolean',
    ];

    /**
     * Boot function to generate reference automatically
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->reference)) {
                $payment->reference = 'SUB-PAY-' . strtoupper(Str::random(20));
            }
        });
    }

    /**
     * Get the user that owns the payment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subscription plan
     */
    public function subscription_plan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     * Get all disputes for this payment
     */
    public function disputes()
    {
        return $this->hasMany(PaymentDispute::class, 'subscription_payment_id');
    }

    /**
     * Get the admin who reconciled this payment
     */
    public function reconciledBy()
    {
        return $this->belongsTo(User::class, 'reconciled_by');
    }

    /**
     * Check if payment is completed
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if payment is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payment failed
     */
    public function isFailed()
    {
        return $this->status === 'failed';
    }

    /**
     * Check if payment is reconciled
     */
    public function isReconciled()
    {
        return $this->is_reconciled;
    }

    /**
     * Mark payment as reconciled
     */
    public function markAsReconciled(int $adminId, ?string $notes = null)
    {
        $this->update([
            'is_reconciled' => true,
            'reconciled_by' => $adminId,
            'reconciled_at' => now(),
            'reconciliation_notes' => $notes,
        ]);
    }

    /**
     * Scope for unreconciled payments
     */
    public function scopeUnreconciled($query)
    {
        return $query->where('is_reconciled', false);
    }

    /**
     * Scope for reconciled payments
     */
    public function scopeReconciled($query)
    {
        return $query->where('is_reconciled', true);
    }

    /**
     * Scope for disputed payments
     */
    public function scopeDisputed($query)
    {
        return $query->where('has_dispute', true);
    }
}
