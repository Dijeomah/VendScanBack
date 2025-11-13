<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'business_link_id',
        'table_id',
        'server_id',
        'vendor_id',
        'customer_name',
        'customer_phone',
        'subtotal',
        'tax',
        'total',
        'notes',
        'payment_status',
        'payment_method',
        'payment_at',
        'status',
    ];

    protected $casts = [
        'payment_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Boot method to generate order number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(uniqid());
            }
        });
    }

    /**
     * Get the business link
     */
    public function businessLink(): BelongsTo
    {
        return $this->belongsTo(BusinessLink::class, 'business_link_id');
    }

    /**
     * Get the table
     */
    public function table(): BelongsTo
    {
        return $this->belongsTo(TableLinkQrData::class, 'table_id');
    }

    /**
     * Get the server
     */
    public function server(): BelongsTo
    {
        return $this->belongsTo(User::class, 'server_id');
    }

    /**
     * Get the vendor
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    /**
     * Get the order items
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the payment
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Scope for vendor's orders
     */
    public function scopeForVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    /**
     * Scope for server's orders
     */
    public function scopeForServer($query, $serverId)
    {
        return $query->where('server_id', $serverId);
    }

    /**
     * Scope for business orders
     */
    public function scopeForBusiness($query, $businessLinkId)
    {
        return $query->where('business_link_id', $businessLinkId);
    }
}
