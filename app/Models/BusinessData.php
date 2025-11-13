<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessData extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'geofence_enabled' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Get the business link that owns this business data
     */
    public function business_link(): BelongsTo
    {
        return $this->belongsTo(BusinessLink::class, 'business_link_id');
    }

    public function vendor_data(): BelongsTo
    {
        return $this->belongsTo(VendorData::class);
    }
}
