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
            'code' => $this->resource->prd_code,
            'name' => $this->resource->prd_name,
            'group' => $this->resource->group,
            'qty' => $this->resource->qty,
            'qty_detail' => $this->resource->qty_detail,
            'original_quantity' => $this->resource->original_quantity,
            'qtyTypeId' => $this->resource->quantity_type_id,
            'qtyType' => $this->resource->qty_type,
            'price' => bcdiv($this->resource->price_cents_per_quantity, 100, 2),
            'status' => $this->resource->status,
            'customer_notes' => $this->resource->customer_notes,
            'supplier_notes' => $this->resource->supplier_notes,
            'best_before_date' => $this->resource->best_before_date,
            'currency_symbol' => $this->resource->currency_symbol,
            'packed_on_date' => $this->resource->packed_on_date,
            'tax_applicable' => $this->resource->tax_applicable,
            'unit_of_order' => $this->resource->unit_of_order,
            'use_by_date' => $this->resource->use_by_date,
            'cost_cents' => $this->resource->cost_cents,
            'customer_order_type' => $this->resource->customer_order_type,
            '_destroy' => false,
        ];
    }
}
