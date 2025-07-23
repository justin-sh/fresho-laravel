<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

/**
 * @property Uuid $id
 * @property String $code
 * @property String $name
 * @property integer $cost
 * @property String $price_text
 * @property Uuid $product_id
 * @property String $qty_type
 * @property String hoc_code
 */
class FreshoProduct extends Model
{
    use HasFactory, HasTimestamps, HasUuids;

    protected static function booted()
    {
        parent::booted();
        static::unguard();
    }
}
