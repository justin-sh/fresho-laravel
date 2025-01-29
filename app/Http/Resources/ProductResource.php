<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Product $resource
 */
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        $whs = [];

//        $loadedWhs = $this->whenLoaded('warehouses');
//        collect($loadedWhs)->each(function ($w) use (&$whs) {
//            $whs[$w->code] = $w->pivot->onhand_qty;
//        });

        return [
            'id' => $this->resource->id,
            'code' => $this->resource->code,
            'cat' => $this->resource->mkt_cat,
            'name' => $this->resource->name,
            'qty_type' => $this->resource->qty_type,
//            $this->merge($whs),
        ];
    }
}
