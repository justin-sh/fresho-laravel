<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_warehouse', function (Blueprint $table) {
            $table->uuid('product_id');
            $table->uuid('warehouse_id');
            $table->integer('onhand_qty')->default(0);
            $table->integer('free_qty')->default(0);
            $table->primary(['product_id', 'warehouse_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_warehouse');
    }
};
