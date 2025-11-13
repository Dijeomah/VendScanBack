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
     * Check if user can create more businesses based on their tier
     */
    public function canCreateBusiness(): bool
    {
        $currentCount = $this->business_links()->count();

        return match ($this->subscription_tier) {
            'free' => $currentCount < 3,
            'pro' => $currentCount < 5,
            'max' => true, // unlimited
            default => $currentCount < 3
        };
    }

    public function business_links(): HasMany
    {
        return $this->hasMany(BusinessLink::class, 'uid');
    }

    /**
     * Get remaining business slots
     */
    public function getRemainingBusinessSlotsAttribute(): int
    {
        $currentCount = $this->business_links()->count();

        return match ($this->subscription_tier) {
            'free' => max(0, 3 - $currentCount),
            'pro' => max(0, 5 - $currentCount),
            'max' => PHP_INT_MAX, // unlimited
            default => max(0, 3 - $currentCount)
        };
    }

}
