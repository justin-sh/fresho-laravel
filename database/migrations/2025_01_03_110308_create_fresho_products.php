<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fresho_products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 32)->index();
            $table->string('name', 512)->index();
            $table->integer('cost')->default(0);
            $table->string('price_text')->nullable();
            $table->uuid('product_id');
            $table->string('unit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fresho_products');
    }
};
