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
            'products'=>$this->products,
            'prices'=>$this->prices,
            'quantity_types'=>$this->quantity_types,
            'product_items'=>$this->product_items,
        ];
    }
}
