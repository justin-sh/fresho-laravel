<?php

namespace App\Http\Resources;

use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property OrderDetail $resource
 */
class OrderDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'product_id' => $this->resource->product_id,
            'name' => $this->resource->prd_name,
            'group' => $this->resource->group,
            'qty' => $this->resource->qty,
            'qtyType' => $this->resource->qty_type,
            'price' => bcdiv($this->resource->price_cents_per_quantity, 100, 2),
            'status' => $this->resource->status,
            'customer_notes' => $this->resource->customer_notes,
            'supplier_notes' => $this->resource->supplier_notes,
        ];
    }
}
