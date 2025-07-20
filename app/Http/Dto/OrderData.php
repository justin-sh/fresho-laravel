<?php

namespace App\Http\Dto;

use App\Models\Order;


class OrderData
{
    public ?string $additional_notes;
    public ?string $buying_list_id = null;
    public bool $can_edit_due_date = false;
    public bool $cancellable = true;
    public bool $charge_credit_card = false;
    public bool $charge_customer_credit_card_fee = false;
    public bool $chargeable = false;
    public ?string $contact_name;
    public ?string $contact_phone;
    public ?string $created_at = null;
    public ?string $currency_symbol = null;
    public bool $customer_may_comment = false;
    public bool $customer_may_finalise = false;
    public bool $customer_may_mark_never_to_be_invoiced = false;
    public bool $customer_may_refinalise = false;
    public bool $customer_payment_method_available = false;
    public ?string $delivery_address;
    public string $delivery_date; // yyyy-MM-dd
    public ?string $delivery_date_message = null;
    public ?string $delivery_instructions = '';
    public string $delivery_method = 'Delivery';
    public string $delivery_or_dispatch_date_text = 'Delivery Date';  // 'Delivery Date'
    public ?string $delivery_run_code; // EE
    public int $delivery_run_position; // 28
    public ?string $delivery_venue;
    public int $discount_percent = 0;
    public ?string $display_number = null;
    public ?string $due_date = null;
    public ?string $external_reference;
    public bool $finalised = false;
    public string $formatted_cached_payable_total; // $406.40
    public ?string $freight_rule = null; //require_freight
    public ?string $freight_total_in_cents = null;
    public bool $has_credit_card_fee = false;
    public bool $has_zero_price_product_orders = false;
    public bool $invoice_confirmed = false;
    public bool $invoiced = false;
    public bool $is_credit_note = false;
    public bool $is_locked = false;
    public bool $is_showing_charge_history = false;
    public bool $is_showing_prices = false;
    public ?string $last_activity_at = null;
    public ?string $latest_charge_state = null;
    public bool $may_mark_as_paid = false;
    public bool $never_to_be_invoiced = false;
    public ?string $number_of_boxes = null;
    public string $order_number;
    public bool $paid = false;
    public ?string $parent_order_id = null;
    public bool $payment_method_available = false;
    public bool $picked = false;
    public bool $picked_post_stocktake = false;
    public ?string $picking_instructions = '';
    public ?string $placed_by_name;
    public string $prefixed_order_number; // F42026377
    /**
     * @var array<OrderItemData> $product_orders_attributes product order items details
     */
    public array $product_orders_attributes;
    public bool $purchase_reconciliation_enabled = false;
    public bool $received_post_stocktake = false;
    public string $receiving_company_id;
    public string $receiving_company_name;
    public string $selling_company_id = 'b181ee08-2214-46ec-ad1e-926a2bbfb8fb';
    public string $selling_company_name = 'House Of Carnivore Pty Ltd';
    public bool $should_finalise = false;
    public bool $should_pick = false;
    public bool $standing_order_enabled = false;
    public bool $start_as_invoice = false;

    public string $state = 'submitted';
    public ?string $status_icons = '';
    public ?string $submitted_at; // yyyy-MM-dd
    public string $supplier_id = '34b3d836-d88d-43b0-87d2-de05bbfc83eb';
    public ?string $supplier_orders_emails = null;
    public float $tax_rate = 0.1;
    public ?string $token = null;
    public ?string $undiscount_total_before_tax_in_cents = null;
    public ?string $updated_at = null;

    public function __construct(Order $order, array $details)
    {
        $this->additional_notes = $order->additional_notes;
        $this->delivery_date = $order->delivery_date->format('Y-m-d');
        $this->delivery_instructions = $order->delivery_instructions;
        if($order->number_of_boxes > 0){
            $this->number_of_boxes = strval($order->number_of_boxes);
        }
        $this->contact_name = $order->contact_name;
        $this->contact_phone = $order->contact_phone;
        $this->delivery_address = $order->delivery_address;
        $this->delivery_run_code = $order->delivery_run;
        $this->delivery_run_position = $order->delivery_run_position;
        $this->delivery_venue = $order->delivery_venue;
        $this->external_reference = $order->external_reference;
        $this->formatted_cached_payable_total = $order->formatted_cached_payable_total;
        $this->freight_rule = $order->freight_rule;
        $this->order_number = $order->order_number;
        $this->picking_instructions = $order->picking_instructions;
        $this->placed_by_name = $order->placed_by_name;
        $this->prefixed_order_number = 'F' . $order->order_number;
        $this->receiving_company_id = $order->receiving_company_id;
        $this->receiving_company_name = $order->receiving_company_name;
        $this->state = $order->state->value;
        $this->submitted_at = $order->submitted_at?->format('Y-m-d');

        $this->product_orders_attributes = $details;
    }
}
