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
 * @property string $receiving_company_id
 * @property string $receiving_company_name
 * @property string $contact_name
 * @property string $contact_phone
 * @property string $delivery_address
 * @property array $details
 * @property OrderState $state
 * @property int $number_of_boxes
 * @property string $formatted_cached_payable_total
 * @property int $payable_total_in_cents
 * @property Date $submitted_at
 * @property string $delivery_method
 * @property string $delivery_venue
 * @property string $external_reference
 * @property string $delivery_instructions
 * @property string $picking_instructions
 * @property string $additional_notes
 * @property string $delivery_run
 * @property int $delivery_run_position
 * @property Date $delivery_at
 * @property string $delivery_by
 * @property string $delivery_proof
 * @property string $parent_order_id
 * @property boolean $is_credit_note
 * @property string $freight_rule
 * @property string $placed_by_name
 * @property boolean $is_locked
 */
class Order extends Model
{
    use HasFactory, HasTimestamps, HasUuids;

    protected $casts = [
        'state' => OrderState::class,
        'delivery_date' => 'date:Y-m-d',
        'delivery_at' => 'datetime',
        'submitted_at' => 'datetime',
        'is_locked' => 'bool',
    ];

    protected static function booted()
    {
        parent::booted();
        static::unguard();
    }

    public function details(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'order_number', 'order_number')
            ->orderBy('group')
            ->orderBy('prd_name');
    }
}
