<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SaleOrder extends Model
{
    use HasFactory, HasUuids, Timestamp;

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'sale_order_details', 'so_id', 'prd_id')
            ->withTimestamps();
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'wh_id');
    }
}
