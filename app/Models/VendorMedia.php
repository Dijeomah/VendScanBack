<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;

    class VendorMedia extends Model
    {
        use HasFactory;

        protected $fillable = [
            'vendor_id',
            'logo',
            'hero'
        ];

//        public function user(): BelongsTo
//        {
//            return $this->belongsTo(User::class, 'id');
//        }
    }
