<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Server extends Model
{
    use HasFactory;

    protected $table = 'users';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'role',
        'status',
        'user_id', // vendor who created this server
    ];

    protected $attributes = [
        'role' => 'server',
    ];

    /**
     * Boot method to add global scope
     */
    protected static function booted()
    {
        static::addGlobalScope('role', function ($query) {
            $query->where('role', 'server');
        });
    }

    /**
     * Get the vendor who created this server
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the businesses this server is assigned to
     */
    public function businesses(): BelongsToMany
    {
        return $this->belongsToMany(BusinessLink::class, 'business_servers', 'server_id', 'business_link_id')
            ->withPivot('status', 'vendor_id')
            ->withTimestamps();
    }

    /**
     * Get the business server assignments
     */
    public function businessServers(): HasMany
    {
        return $this->hasMany(BusinessServer::class, 'server_id');
    }

    /**
     * Get the table assignments for this server
     */
    public function tableAssignments(): HasMany
    {
        return $this->hasMany(ServerTableAssignment::class, 'server_id');
    }
}
