<?php

namespace App\Http\Dto;

use Illuminate\Support\Facades\Log;

class OrderItemData
{
    public ?string $best_before_date;
    public int $cost_cents;
    public ?string $created_at;
    public string $currency_symbol = "$";
    public string $customer_order_id = "addfbb68-fd04-48fa-9f54-49aa1d482cdd";
    public string $customer_order_type = "SupplierOrder";
    public string $id;
    public ?string $notes; //"麻烦只要猪颈骨，不要背甲骨。谢谢";
    public ?float $original_quantity;//3;
    public ?string $packed_on_date;
    public int $price_per_quantity;
    public string $product_code;// "1076";
    public string $product_group;// "Boning";
    public string $product_id;// "9ce56314-6b31-40fb-b6ff-2af472ee0feb";
    public string $product_name;//"Pork Neck Bones Meaty Cut 普通多肉猪颈骨";
    public float $quantity;//3;
    public string $quantity_type_id;// "94ab9910-b3ea-495c-a740-198b0364a92f";
    public string $quantity_type_name;//"Kg";
    public string $supplied_status;//"supplied";
    public ?string $supplier_notes;
    public bool $tax_applicable = false;
    public ?string $unit_of_order;
    public ?string $updated_at;
    public ?string $use_by_date;
    public bool $_destroy = false;

    public function __construct(array $detail, string $orderId)
    {
        Log::debug(json_encode($detail));
        $this->best_before_date = $detail['best_before_date'];
        $this->cost_cents = $detail['cost_cents'];
        $this->created_at = null;
        $this->currency_symbol = $detail['currency_symbol'];
        $this->customer_order_id = $orderId; //$detail['currency_symbol'];
        $this->id = $detail['id'];
        $this->notes = $detail['customer_notes'];
        $this->original_quantity = $detail['original_quantity'];
        $this->packed_on_date = $detail['packed_on_date'];
        $this->price_per_quantity = $detail['price'];
        $this->product_code = $detail['code'];
        $this->product_group = $detail['group'];
        $this->product_id = $detail['product_id'];
        $this->product_name = $detail['name'];
        $this->quantity = $detail['qty'];
        $this->quantity_type_id = $detail['qtyTypeId'];
        $this->quantity_type_name = $detail['qtyType'];
        $this->supplied_status = $detail['status'];
        $this->supplier_notes = $detail['supplier_notes'];
        $this->tax_applicable = $detail['tax_applicable'];
        $this->unit_of_order = $detail['unit_of_order'] ?? '';
        $this->use_by_date = $detail['use_by_date'];
        $this->_destroy = $detail['_destroy'];
    }
}
