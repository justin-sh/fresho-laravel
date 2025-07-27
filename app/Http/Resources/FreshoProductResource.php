<?php

namespace App\Http\Resources;

use App\Models\FreshoProduct;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property FreshoProduct $resource
 */
class FreshoProductResource extends JsonResource
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
            'code' => $this->resource->code,
            'cat' => $this->resource->mkt_cat,
            'name' => $this->resource->name,
            'qty_type' => $this->resource->qty_type,
            'hoc_code' => $this->resource->hoc_code,
            'hoc_name' => $this->resource->hoc_name,
            'unit_map_ratio' => $this->resource->unit_map_ratio,
//            $this->merge($whs),
        ];
    }
}
