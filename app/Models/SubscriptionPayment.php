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
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'paystack_data' => 'array',
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
}
