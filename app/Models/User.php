<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
//    protected $fillable = [
//        'name',
//        'role',
//        'subscription_tier',
//        'business_limit',
//        'phone_number',
//        'email',
//        'password',
//    ];
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function user_data(): HasMany
    {
        return $this->hasMany(UserData::class, 'uid');
    }

    public function item(): HasMany
    {
        return $this->hasMany(Item::class, 'category_id');
    }

    public function vendor_media(): HasOne
    {
        return $this->hasOne(VendorMedia::class, 'vendor_id');
    }

    /**
     * Servers that belong to this vendor (for vendors)
     */
    public function servers(): HasMany
    {
        return $this->hasMany(User::class, 'created_by')->where('role', 'server');
    }

    /**
     * Businesses that this server is assigned to (for servers)
     */
    public function assigned_businesses(): HasMany
    {
        return $this->hasMany(BusinessServer::class, 'server_id');
    }

    /**
     * Tables that this server is assigned to (for servers)
     */
    public function assigned_tables(): HasMany
    {
        return $this->hasMany(ServerTableAssignment::class, 'server_id');
    }

    /**
     * Get the user's subscription plan
     */
    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     * Get the user's active subscription
     */
    public function subscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'active');
    }

    /**
     * Get all user's subscriptions
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function business_links(): HasMany
    {
        return $this->hasMany(BusinessLink::class, 'uid');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'user_id');
    }

    /**
     * Check if user can create more of a specific resource
     */
    public function canCreate($resource): bool
    {
        $plan = $this->subscriptionPlan;

        if (!$plan) {
            // No plan assigned, default to free tier limits
            $plan = SubscriptionPlan::where('slug', 'free')->first();
        }

        $limit = $plan->getLimit($resource);

        // Unlimited
        if ($limit === null) {
            return true;
        }

        $currentCount = $this->getResourceCount($resource);

        return $currentCount < $limit;
    }

    /**
     * Get current count of a resource
     */
    public function getResourceCount($resource): int
    {
        return match ($resource) {
            'businesses' => $this->business_links()->count(),
            'tables' => TableLinkQrData::whereIn('business_link_id',
                $this->business_links()->pluck('id'))->count(),
            'servers' => User::where('created_by', $this->id)
                ->where('role', 'server')->count(),
            'items' => Item::whereIn('business_link_id',
                $this->business_links()->pluck('id'))->count(),
            default => 0,
        };
    }

    /**
     * Get remaining slots for a resource
     */
    public function getRemainingSlots($resource): int|string
    {
        $plan = $this->subscriptionPlan;

        if (!$plan) {
            $plan = SubscriptionPlan::where('slug', 'free')->first();
        }

        $limit = $plan->getLimit($resource);

        // Unlimited
        if ($limit === null) {
            return 'unlimited';
        }

        $currentCount = $this->getResourceCount($resource);

        return max(0, $limit - $currentCount);
    }

    /**
     * Check if user has access to a feature
     */
    public function hasFeature($feature): bool
    {
        $plan = $this->subscriptionPlan;

        if (!$plan) {
            return false;
        }

        return $plan->hasFeature($feature);
    }

    /**
     * Check if user is on free plan
     */
    public function isOnFreePlan(): bool
    {
        return $this->subscriptionPlan && $this->subscriptionPlan->isFree();
    }

    /**
     * Check if user is on pro plan
     */
    public function isOnProPlan(): bool
    {
        return $this->subscriptionPlan && $this->subscriptionPlan->isPro();
    }

    /**
     * Check if user is on enterprise plan
     */
    public function isOnEnterprisePlan(): bool
    {
        return $this->subscriptionPlan && $this->subscriptionPlan->isEnterprise();
    }

    /**
     * Legacy method - kept for backward compatibility
     * @deprecated Use canCreate('businesses') instead
     */
    public function canCreateBusiness(): bool
    {
        return $this->canCreate('businesses');
    }

    /**
     * Legacy method - kept for backward compatibility
     * @deprecated Use getRemainingSlots('businesses') instead
     */
    public function getRemainingBusinessSlotsAttribute(): int|string
    {
        return $this->getRemainingSlots('businesses');
    }

}
