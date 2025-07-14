<?php

namespace App\Http\Dto;

class OrderData
{
    public string $additional_notes;
    private string $buying_list_id;
    private bool $can_edit_due_date = false;
    private bool $cancellable = true;
    private bool $charge_credit_card = false;
    private bool $charge_customer_credit_card_fee = false;
    private bool $chargeable = false;
    private string $contact_name;
    private string $contact_phone;
    private string $created_at;
    private string $currency_symbol;
    private bool $customer_may_comment = false;
    private bool $customer_may_finalise = false;
    private bool $customer_may_mark_never_to_be_invoiced = false;
    private bool $customer_may_refinalise = false;
    private bool $customer_payment_method_available = false;
    private string $delivery_address;
    private string $delivery_date; // yyyy-MM-dd
    private string $delivery_date_message;
    private string $delivery_instructions = '';
    private string $delivery_method = 'Delivery';
    private string $delivery_or_dispatch_date_text;  // 'Delivery Date'
    private string $delivery_run_code; // EE
    private int $delivery_run_position; // 28
    private string $delivery_venue;
    private int $discount_percent = 0;
    private string $display_number;
    private string $due_date;
    private string $external_reference;
    private bool $finalised = false;
    private string $formatted_cached_payable_total; // $406.40
    private string $freight_rule; //require_freight
    private string $freight_total_in_cents;
    private bool $has_credit_card_fee = false;
    private bool $has_zero_price_product_orders = false;
    private bool $invoice_confirmed = false;
    private bool $invoiced = false;
    private bool $is_credit_note = false;
    private bool $is_locked = false;
    private bool $is_showing_charge_history = false;
    private bool $is_showing_prices = false;
    private string $last_activity_at;
    private string $latest_charge_state;
    private bool $may_mark_as_paid = false;
    private bool $never_to_be_invoiced = false;
    private string $number_of_boxes;
    private string $order_number;
    private bool $paid = false;
    private string $parent_order_id;
    private bool $payment_method_available = false;
    private bool $picked = false;
    private bool $picked_post_stocktake = false;
    private string $picking_instructions = '';
    private string $placed_by_name;
    private string $prefixed_order_number; // F42026377
    /**
     * @var array<OrderItemData> $product_orders_attributes product order items details
     */
    private array $product_orders_attributes;
    private bool $purchase_reconciliation_enabled = false;
    private bool $received_post_stocktake = false;
    private string $receiving_company_id;
    private string $receiving_company_name;
    private string $selling_company_id = 'b181ee08-2214-46ec-ad1e-926a2bbfb8fb';
    private string $selling_company_name = 'House Of Carnivore Pty Ltd';
    private bool $should_finalise = false;
    private bool $should_pick = false;
    private bool $standing_order_enabled = false;
    private bool $start_as_invoice = false;

    private string $state = 'submitted';
    private string $status_icons = '';
    private string $submitted_at; // yyyy-MM-dd
    private string $supplier_id = '34b3d836-d88d-43b0-87d2-de05bbfc83eb';
    private string $supplier_orders_emails;
    private float $tax_rate = 0.1;
    private string $token;
    private string $undiscount_total_before_tax_in_cents;
    private string $updated_at;
}
