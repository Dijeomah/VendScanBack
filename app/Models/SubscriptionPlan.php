<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'max_businesses',
        'max_tables',
        'max_servers',
        'max_items',
        'has_analytics',
        'has_custom_branding',
        'has_priority_support',
        'has_api_access',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'max_businesses' => 'integer',
        'max_tables' => 'integer',
        'max_servers' => 'integer',
        'max_items' => 'integer',
        'has_analytics' => 'boolean',
        'has_custom_branding' => 'boolean',
        'has_priority_support' => 'boolean',
        'has_api_access' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the subscriptions for this plan
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the users subscribed to this plan
     */
    public function users()
    {
        return $this->hasMany(User::class, 'subscription_plan_id');
    }

    /**
     * Check if this is the free plan
     */
    public function isFree()
    {
        return $this->slug === 'free';
    }

    /**
     * Check if this is the pro plan
     */
    public function isPro()
    {
        return $this->slug === 'pro';
    }

    /**
     * Check if this is the enterprise plan
     */
    public function isEnterprise()
    {
        return $this->slug === 'enterprise';
    }

    /**
     * Check if a feature is available in this plan
     */
    public function hasFeature($feature)
    {
        return $this->$feature ?? false;
    }

    /**
     * Get the limit for a resource
     */
    public function getLimit($resource)
    {
        $limitField = "max_{$resource}";
        return $this->$limitField;
    }

    /**
     * Check if resource is unlimited
     */
    public function isUnlimited($resource)
    {
        return $this->getLimit($resource) === null;
    }
}
