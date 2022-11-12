<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserData extends Model
{
    use HasFactory;

    public function city():BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function state():BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function business_links():BelongsTo
    {
        return $this->belongsTo(BusinessLink::class, 'id');
//        return $this->belongsTo(BusinessLink::class, 'uid');
//        return $this->belongsTo(BusinessLink::class, 'uid');
    }

    public function food():BelongsTo
    {
        return $this->belongsTo(Food::class, 'uid');
//        return $this->belongsTo(BusinessLink::class, 'uid');
    }

}
