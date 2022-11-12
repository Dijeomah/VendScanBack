<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Food extends Model
{
    use HasFactory;

//    public function user(){
//        return $this->belongsTo(User::class, 'id');
//    }

    public function category():BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
