<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TableLinkQrData extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function business_link():BelongsTo
    {
        return $this->belongsTo(BusinessLink::class);
    }

}
