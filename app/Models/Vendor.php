<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Vendor extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];


    public function vendorData():HasMany
    {
        return $this->hasMany(VendorData::class, 'v_id');
    }

    public function business_data():HasMany
    {
        return $this->hasMany(BusinessData::class, 'bd_id');
    }

    public function business_links():HasMany
    {
        return $this->hasMany(BusinessLink::class, 'uid');
    }

    public function item():HasMany
    {
        return $this->hasMany(Item::class, 'category_id');
    }

    public function vendor_media():HasOne
    {
        return $this->hasOne(VendorMedia::class, 'vendor_id');
    }

    public function categories():HasMany
    {
        return $this->hasMany(Category::class, 'user_id');
    }

}
