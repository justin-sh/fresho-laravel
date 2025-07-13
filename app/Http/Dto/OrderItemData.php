<?php

namespace App\Http\Dto;

class OrderItemData
{
    private string $best_before_date;
    private int $cost_cents;
    private string $created_at;
    private string $currency_symbol = "$";
    private string $customer_order_id = "addfbb68-fd04-48fa-9f54-49aa1d482cdd";
    private string $customer_order_type = "SupplierOrder";
    private string $id;
    private string $notes; //"麻烦只要猪颈骨，不要背甲骨。谢谢";
    private float $original_quantity;//3;
    private string $packed_on_date;
    private int $price_per_quantity;
    private string $product_code;// "1076";
    private string $product_group;// "Boning";
    private string $product_id;// "9ce56314-6b31-40fb-b6ff-2af472ee0feb";
    private string $product_name;//"Pork Neck Bones Meaty Cut 普通多肉猪颈骨";
    private float $quantity;//3;
    private string $quantity_type_id;// "94ab9910-b3ea-495c-a740-198b0364a92f";
    private string $quantity_type_name;//"Kg";
    private string $supplied_status;//"supplied";
    private string $supplier_notes;
    private bool $tax_applicable = false;
    private string $unit_of_order;
    private string $updated_at;
    private string $use_by_date;
    private bool $_destroy = false;
}
