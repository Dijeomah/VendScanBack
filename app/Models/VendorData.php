<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VendorData extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function business_data():HasMany
    {
        return $this->hasMany(BusinessData::class, 'bd_id');
    }
}
