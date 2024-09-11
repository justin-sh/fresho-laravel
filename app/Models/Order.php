<?php

namespace App\Models;

use Carbon\Traits\Date;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Nonstandard\Uuid;

/**
 * @property Uuid $id
 * @property string $order_number
 * @property Date $delivery_date
 * @property string $receiving_company_name
 * @property OrderState $state
 * @property string $delivery_run
 * @property Date $delivery_at
 * @property string $delivery_by
 * @property string $delivery_proof
 */
class Order extends Model
{
    use HasFactory, HasTimestamps, HasUuids;

    protected $casts = [
        'state' => OrderState::class,
        'delivery_date' => 'date:Y-m-d',
        'delivery_at' => 'datetime',
    ];

    protected static function booted()
    {
        parent::booted();
        static::unguard();
    }

    public function details(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'order_number', 'order_number');
    }
}
