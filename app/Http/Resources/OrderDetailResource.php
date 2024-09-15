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
            'name' => $this->resource->prd_name,
            'group' => $this->resource->group,
            'qty' => $this->resource->qty,
            'qtyType' => $this->resource->qty_type,
            'status' => $this->resource->status,
        ];
    }
}
