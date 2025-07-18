<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

/**
 * @property Order $resource
 */
class OrderResource extends JsonResource
{

    private array $quantity_types = [];
    private array $products = [];
    private array $prices = [];
    private array $product_items = [];

    public function __construct($resource,
                                array $quantity_types = [],
                                array $products = [],
                                array $prices = [],
                                array $product_items = [])
    {
        parent::__construct($resource);
        $this->quantity_types = $quantity_types;
        $this->products = $products;
        $this->prices = $prices;
        $this->product_items = $product_items;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $prds = collect($this->products)->map(fn($v)=>$this->unsetIdFromArray($v))->collapseWithKeys();
        $prices = collect($this->prices)->map(fn($v)=>$this->unsetIdFromArray($v))->collapseWithKeys();
        $qtyTypes = collect($this->quantity_types)->map(fn($v)=>$this->unsetIdFromArray($v))->collapseWithKeys();
        $product_items = collect($this->product_items)->map(fn($v)=>$this->unsetIdFromArray($v))->collapseWithKeys();
//        Log::debug(json_encode($prices));

        return [
            'id' => $this->resource->id,
            'orderNo' => $this->resource->order_number,
            'deliveryDate' => $this->resource->delivery_date->toDateString(),
            'customer' => $this->resource->receiving_company_name,
            'state' => $this->resource->state->name,
            'deliveryMethod' => $this->resource->delivery_method,
            'deliveryInstructions' => $this->resource->delivery_instructions,
            'pickingInstructions' => $this->resource->picking_instructions,
            'additionalNotes' => $this->resource->additional_notes,
            'numberOfBoxes' => $this->resource->number_of_boxes,
            'run' => $this->resource->delivery_run,
            'by' => $this->resource->delivery_by,
            'at' => $this->resource->delivery_at,
            'proof' => $this->resource->delivery_proof,
            'product_orders' => OrderDetailResource::collection($this->resource->details),
            'products'=>$prds,
            'prices'=>$prices,
            'quantity_types'=>$qtyTypes,
            'product_items'=>$product_items,
        ];
    }

    private function unsetIdFromArray($arr): array
    {
        $id = $arr['id'];
        unset($arr['id']);
        return [$id => $arr];
    }
}
