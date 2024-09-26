<?php

namespace App\Http\Resources;

use App\Models\SaleOrder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property SaleOrder $resource
 */
class SaleOrderResource extends JsonResource
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
            'title' => $this->resource->title,
            'pickupAt' => $this->resource->pickup_at,
            'qty' => $this->resource->qty,
            'state' => $this->resource->state,
            'whId' => $this->resource->wh_id,
            'wh' => $this->whenLoaded('warehouse', function ($warehouse) {
                return $warehouse->code;
            }),
            'details' => $this->whenLoaded('products', function ($details) {
                return collect($details)->map(fn($x) => ['prdId' => $x->id, 'cat' => $x->cat, 'qty' => $x->pivot->qty, 'location' => $x->pivot->location, 'comment' => $x->pivot->comment]);
            }),
        ];
    }
}
