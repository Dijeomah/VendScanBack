<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessData extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function vendor_data(): BelongsTo
    {
        return $this->belongsTo(VendorData::class);
    }
}
