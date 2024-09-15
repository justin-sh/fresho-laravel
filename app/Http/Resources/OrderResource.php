<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Order $resource
 */
class OrderResource extends JsonResource
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
            'orderNo' => $this->resource->order_number,
            'deliveryDate' => $this->resource->delivery_date->toDateString(),
            'customer' => $this->resource->receiving_company_name,
            'state' => $this->resource->state,
            'run' => $this->resource->delivery_run,
            'by' => $this->resource->delivery_by,
            'at' => $this->resource->delivery_at,
            'proof' => $this->resource->delivery_proof,
            'products' => OrderDetailResource::collection($this->resource->details),
        ];
    }
}
