<?php

namespace App\Models;

use App\SalesStatus;
use Carbon\Carbon;
use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property  string $id
 * @property  string $title
 * @property  Carbon $pickup_at
 * @property int $qty
 * @property SalesStatus $state
 * @property string $wh_id
 * @property Product[] $products
 */
class SaleOrder extends Model
{
    use HasFactory, HasUuids, Timestamp;

    protected $fillable = ['title', 'qty', 'pickup_at'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'sale_order_details', 'so_id', 'prd_id')
            ->withPivot(['qty', 'location', 'comment'])
            ->withTimestamps()
            ->orderByPivot('row_no');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'wh_id');
    }
}
