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
        Schema::table('order_details', function (Blueprint $table) {
            $table->date('best_before_date')->nullable();
            $table->date('packed_on_date')->nullable();
            $table->date('use_by_date')->nullable();
            $table->string('currency_symbol')->default('$')->nullable();
            $table->string('customer_order_type')->default('SupplierOrder')->nullable();
            $table->string('unit_of_order');
            $table->boolean('tax_applicable')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn('best_before_date');
            $table->dropColumn('packed_on_date');
            $table->dropColumn('use_by_date');
            $table->dropColumn('currency_symbol');
            $table->dropColumn('customer_order_type');
            $table->dropColumn('unit_of_order');
            $table->dropColumn('tax_applicable');
        });
    }
};
