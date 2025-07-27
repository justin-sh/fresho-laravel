<?php

namespace App\Models;

use App\ProductCategory;
use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

/**
 * @property Uuid $id
 * @property string $code
 * @property string $name
 * @property ProductCategory $cat
 * @property int $onhand_qty
 * @property int $free_qty
 * @property string $comment
 * @property string base_unit
 * @property \DateTime sync_time
 */
class Product extends Model
{
    use HasFactory, Timestamp, HasUuids;

    protected $casts = [
        'cat' => ProductCategory::class,
        'sync_time' => 'datetime'
    ];

    protected static function booted()
    {
        parent::booted();
        static::unguard();
    }
}
