<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'amount',
        'payment_method',
        'payment_status',
        'transaction_reference',
        'payment_details',
        'payment_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_at' => 'datetime',
        'payment_details' => 'array',
    ];

    /**
     * Boot method to generate transaction reference
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->transaction_reference)) {
                $payment->transaction_reference = 'TXN-' . strtoupper(uniqid());
            }
        });
    }

    /**
     * Get the order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
