<?php

use App\PurchaseStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title', 128)->unique();
            $table->date('arrival_at');
            $table->integer('qty')->default(0);
            $table->enum('state', array_column(PurchaseStatus::cases(), 'value'));
            $table->uuid('wh_id');
            $table->timestamps();
        });
        Schema::create('purchase_order_details', function (Blueprint $table) {
//            $table->uuid('id')->primary();
            $table->uuid('po_id');
            $table->uuid('prd_id');
            $table->integer('row_no')->default(0);
            $table->integer('qty')->default(0);
            $table->string('comment', 128)->nullable();
            $table->timestamps();
            $table->primary(['po_id', 'prd_id', 'row_no']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_details');
        Schema::dropIfExists('purchase_orders');
    }
};
