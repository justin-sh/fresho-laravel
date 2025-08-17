<?php

namespace App\Models;

use Carbon\Traits\Date;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string id
 * @property string order_number
 * @property string prd_code
 * @property string product_id
 * @property string prd_name
 * @property float qty
 * @property string qty_detail
 * @property float original_quantity
 * @property string quantity_type_id
 * @property string qty_type
 * @property int price_cents_per_quantity
 * @property int cost_cents
 * @property string group
 * @property OrderPrdState status
 * @property string customer_notes
 * @property string supplier_notes
 * @property Date best_before_date
 * @property Date packed_on_date
 * @property Date use_by_date
 * @property string currency_symbol
 * @property string customer_order_type
 * @property string unit_of_order
 * @property boolean tax_applicable
 */
class OrderDetail extends Model
{

    use HasUuids;

    public $timestamps = false;

    protected $casts = [
        'status' => OrderPrdState::class,
        'best_before_date' => 'date:Y-m-d',
        'packed_on_date' => 'date:Y-m-d',
        'use_by_date' => 'date:Y-m-d',
        'tax_applicable' => 'boolean',
    ];

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
