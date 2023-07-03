<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    use HasFactory;

//    public function user(){
//        return $this->belongsTo(User::class, 'id');
//    }

    public function category():BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function sub_category():BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }
}
