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

        protected $table = 'vendor_media';

        /**
         * Get the vendor that owns the media
         */
        public function vendor(): BelongsTo
        {
            return $this->belongsTo(User::class, 'vendor_id', 'id');
        }
    }
