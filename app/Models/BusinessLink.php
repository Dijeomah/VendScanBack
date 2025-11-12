<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BusinessLink extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the user (vendor) that owns the business link
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uid', 'id');
    }

    public function vendor_data(): BelongsTo
    {
        return $this->belongsTo(VendorData::class, 'vd_id');
    }

    public function business_links(): HasMany
    {
        return $this->hasMany(BusinessLink::class, 'id');
//        return $this->belongsTo(BusinessLink::class, 'uid');
    }

    public function business_data(): HasOne
    {
        return $this->hasOne(BusinessData::class, 'bd_id');
//        return $this->belongsTo(BusinessLink::class, 'uid');
    }

    public function table_link_qr_data(): HasMany
    {
        return $this->hasMany(TableLinkQrData::class, 'bl_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'category_id');
    }
}
