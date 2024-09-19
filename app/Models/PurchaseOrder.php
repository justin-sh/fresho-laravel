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
 * @property PurchaseStatus $state
 * @property Warehouse $warehouse
 */
class PurchaseOrder extends Model
{
    use HasFactory, HasUuids, Timestamp;

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'purchase_order_details', 'po_id', 'prd_id')
            ->withTimestamps();
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'wh_id');
    }
}
