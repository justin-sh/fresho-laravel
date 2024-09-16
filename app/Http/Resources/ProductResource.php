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
        return [
            'id' => $this->resource->id,
            'cat' =>$this->resource->cat,
            'code' => $this->resource->code,
            'name' => $this->resource->name,
            'onhand_qty' => $this->resource->onhand_qty,
            'comment' => $this->resource->comment,
        ];
    }
}
