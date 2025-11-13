<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServerTableAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'server_id',
        'table_id',
        'business_link_id',
        'status',
        'assigned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    /**
     * Get the server user
     */
    public function server(): BelongsTo
    {
        return $this->belongsTo(User::class, 'server_id');
    }

    /**
     * Get the table
     */
    public function table(): BelongsTo
    {
        return $this->belongsTo(TableLinkQrData::class, 'table_id');
    }

    /**
     * Get the business link
     */
    public function businessLink(): BelongsTo
    {
        return $this->belongsTo(BusinessLink::class, 'business_link_id');
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(BusinessLink::class, 'business_link_id');
    }
}
