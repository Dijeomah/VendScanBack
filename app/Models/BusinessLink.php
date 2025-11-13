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
        return $this->hasMany(TableLinkQrData::class, 'business_link_id');
    }

    /**
     * Alias for table_link_qr_data
     */
    public function tables(): HasMany
    {
        return $this->table_link_qr_data();
    }

    /**
     * Get servers assigned to this business
     */
    public function business_servers(): HasMany
    {
        return $this->hasMany(BusinessServer::class, 'business_link_id');
    }

    /**
     * Get active servers for this business
     */
    public function active_servers()
    {
        return $this->business_servers()->where('status', 'active')->with('server');
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'business_link', 'business_link');
    }
}
