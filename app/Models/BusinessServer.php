<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessServer extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_link_id',
        'server_id',
        'vendor_id',
        'status',
    ];

    /**
     * Get the business link this assignment belongs to
     */
    public function businessLink(): BelongsTo
    {
        return $this->belongsTo(BusinessLink::class, 'business_link_id');
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(BusinessLink::class, 'business_link_id');
    }

    /**
     * Get the server user
     */
    public function server(): BelongsTo
    {
        return $this->belongsTo(User::class, 'server_id');
    }

    /**
     * Get the vendor who made this assignment
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
}
