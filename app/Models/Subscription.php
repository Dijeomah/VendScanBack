<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'status',
        'started_at',
        'expires_at',
        'trial_ends_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'trial_ends_at' => 'datetime',
    ];

    /**
     * Get the user that owns the subscription
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subscription plan
     */
    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    /**
     * Check if subscription is active
     */
    public function isActive()
    {
        return $this->status === 'active' &&
               ($this->expires_at === null || $this->expires_at->isFuture());
    }

    /**
     * Check if subscription is on trial
     */
    public function onTrial()
    {
        return $this->status === 'trial' &&
               $this->trial_ends_at !== null &&
               $this->trial_ends_at->isFuture();
    }

    /**
     * Check if subscription is cancelled
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if subscription is expired
     */
    public function isExpired()
    {
        return $this->status === 'expired' ||
               ($this->expires_at !== null && $this->expires_at->isPast());
    }

    /**
     * Cancel the subscription
     */
    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
    }

    /**
     * Renew the subscription
     */
    public function renew($months = 1)
    {
        $this->update([
            'status' => 'active',
            'expires_at' => $this->expires_at
                ? $this->expires_at->addMonths($months)
                : now()->addMonths($months),
        ]);
    }

    /**
     * Mark subscription as expired
     */
    public function markAsExpired()
    {
        $this->update(['status' => 'expired']);
    }
}
