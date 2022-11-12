<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessLink extends Model
{
    use HasFactory;

    public function user():HasMany
    {
        return $this->hasMany(User::class);
    }

    public function user_data():BelongsTo
    {
        return $this->belongsTo(UserData::class, 'id');
    }

    public function business_links():HasMany
    {
        return $this->hasMany(BusinessLink::class, 'id');
//        return $this->belongsTo(BusinessLink::class, 'uid');
    }
}
