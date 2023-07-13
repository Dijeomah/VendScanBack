<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_name',
        'category_code'
    ];

    public function item(): HasMany
    {
        return $this->hasMany(Item::class, 'category_id');
    }

    public function subCategories(): HasMany
    {
        return  $this->hasMany(SubCategory::class, 'category_id');
    }
}
