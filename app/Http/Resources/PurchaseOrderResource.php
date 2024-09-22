<?php

namespace App\Http\Resources;

use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property PurchaseOrder $resource
 */
class PurchaseOrderResource extends JsonResource
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
            'arrivalAt' => $this->resource->arrival_at,
            'qty' => $this->resource->qty,
            'state' => $this->resource->state,
            'wh' => [
                'id' => $this->resource->warehouse->id,
                'code' => $this->resource->warehouse->code,
            ],
            'details' => $this->whenLoaded('products', function ($details) {
                return collect($details)->map(fn($x) => ['prdId' => $x->id, 'cat' => $x->cat, 'qty' => $x->pivot->qty, 'location' => $x->pivot->location, 'comment' => $x->pivot->comment]);
            }),
        ];
    }
}
