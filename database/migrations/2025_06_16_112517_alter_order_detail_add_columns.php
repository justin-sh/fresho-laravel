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
        Schema::table('order_details', function (Blueprint $table) {
            $table->uuid('id')->nullable()->first();
            $table->uuid('product_id')->nullable()->after('idx');
            $table->decimal('qty',10,3)->change();
            $table->decimal('original_quantity',10,3)->default(0)->after('qty_type');
            $table->integer('price_cents_per_quantity')->default(0)->after('original_quantity');
            $table->integer('cost_cents')->default(0)->after('price_cents_per_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->dropColumn('product_id');
            $table->dropColumn('original_quantity');
            $table->dropColumn('price_per_quantity');
            $table->dropColumn('cost_cents');
        });
    }
};
