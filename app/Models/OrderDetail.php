<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string order_number
 * @property string prd_code
 * @property string prd_name
 * @property float qty
 * @property string qty_type
 * @property string group
 * @property OrderPrdState status
 * @property string customer_notes
 * @property string supplier_notes
 */
class OrderDetail extends Model
{

    public $timestamps = false;

    protected $casts = ['status' => OrderPrdState::class];

    protected static function booted()
    {
        parent::booted();
        static::unguard();
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_number', 'order_number');
    }
}
