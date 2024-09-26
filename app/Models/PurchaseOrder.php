<?php

namespace App\Models;

use App\PurchaseStatus;
use Carbon\Carbon;
use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Ramsey\Uuid\Uuid;

/**
 * @property Uuid $id
 * @property string $title
 * @property Carbon $arrival_at
 * @property int $qty
 * @property string $wh_id
 * @property PurchaseStatus $state
 * @property Product[] $products
 */
class PurchaseOrder extends Model
{
    use HasFactory, HasUuids, Timestamp;

    protected $fillable = ['title', 'qty', 'arrival_at'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'purchase_order_details', 'po_id', 'prd_id')
            ->withPivot(['qty', 'location', 'comment'])
            ->withTimestamps()
            ->orderByPivot('row_no');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'wh_id');
    }
}
