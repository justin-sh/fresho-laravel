<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    protected static function booted()
    {
        parent::booted();
        static::unguard();
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
