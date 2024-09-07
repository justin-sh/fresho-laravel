<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected static function booted()
    {
        parent::booted();
        static::unguard();
    }
}
