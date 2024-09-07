<?php

use App\Models\OrderPrdState;
use App\Models\OrderState;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('order_number', 255)->unique();
            $table->date('delivery_date')->index();
            $table->uuid('receiving_company_id');
            $table->string('receiving_company_name', 255);
            $table->string('additional_notes', 4000)->nullable();
            $table->string('contact_name', 255)->nullable();
            $table->string('contact_phone', 255)->nullable();
            $table->string('delivery_address', 255);
            $table->string('delivery_method', 255);
            $table->string('delivery_venue', 255);
            $table->string('external_reference', 255)->nullable();
            $table->string('delivery_instructions', 255)->nullable();
            $table->string('formatted_cached_payable_total', 255);
            $table->integer('payable_total_in_cents')->default(0);
            $table->dateTime('submitted_at', 6)->nullable();
            $table->enum('state', array_column(OrderState::cases(), 'value'));
            $table->string('placed_by_name', 255)->nullable();
            $table->string('delivery_run', 255)->nullable();
//            $table->json('products')->default([])->nullable();
            $table->dateTime('delivery_at')->nullable();
            $table->string('delivery_by', 255)->nullable();
            $table->string('delivery_proof_url', 255)->nullable();
            $table->string('delivery_proof', 255)->nullable();
            $table->uuid('parent_order_id')->nullable()->comment("indicate if it's a backorder");
            $table->timestamps();
        });

        Schema::create('order_details', function (Blueprint $table) {
            $table->uuid('order_id');
            $table->string('prd_code', 32);
            $table->string('prd_name', 255);
            $table->float('qty');
            $table->string('qty_type', 32);
            $table->string('group', 32);
            $table->enum('status', array_column(OrderPrdState::cases(), 'value'));
            $table->string('customer_notes', 255);
            $table->string('supplier_notes', 255);
            $table->primary(['order_id', 'prd_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
        Schema::dropIfExists('orders_details');
    }
};
