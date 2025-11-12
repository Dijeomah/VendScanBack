<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TableLinkQrData extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'table_link_qr_data';

    /**
     * Get the business this table belongs to
     */
    public function business_link(): BelongsTo
    {
        return $this->belongsTo(BusinessLink::class, 'business_link_id');
    }

    /**
     * Alias for business_link
     */
    public function business(): BelongsTo
    {
        return $this->business_link();
    }

    /**
     * Get the server assignments for this table
     */
    public function server_assignments(): HasMany
    {
        return $this->hasMany(ServerTableAssignment::class, 'table_id');
    }

    /**
     * Get active servers assigned to this table
     */
    public function active_servers()
    {
        return $this->server_assignments()->where('status', 'active')->with('server');
    }

    /**
     * Check if table has any active servers
     */
    public function hasActiveServers(): bool
    {
        return $this->server_assignments()->where('status', 'active')->exists();
    }
}
