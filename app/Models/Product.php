<?php

namespace App\Models;

use App\ProductCategory;
use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Uuid;

/**
 * @property Uuid $id
 * @property string $code
 * @property string $name
 * @property ProductCategory $cat
 * @property int $onhand_qty
 * @property int $free_qty
 * @property string $comment
 * @property array $warehouses
 */
class Product extends Model
{
    use HasFactory, Timestamp, HasUuids;

    protected $casts = ['cat' => ProductCategory::class];

    protected static function booted()
    {
        parent::booted();
        static::unguard();
    }

    public function warehouses(): BelongsToMany
    {
        return $this->belongsToMany(Warehouse::class)
            ->withPivot(['onhand_qty', 'free_qty'])->withTimestamps();
    }

    public function saleOrder(): BelongsToMany
    {
        return $this->belongsToMany(SaleOrder::class, 'sale_order_details', 'prd_id', 'so_id')
            ->withTimestamps();
    }

    public function purchaseOrder(): BelongsToMany
    {
        return $this->belongsToMany(PurchaseOrder::class, 'purchase_order_details','prd_id', 'po_id')
            ->withTimestamps();
    }
}
