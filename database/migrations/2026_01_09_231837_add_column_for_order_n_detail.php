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
            $table->date('delivery_date')->nullable()->after("order_number");
            $table->string("receiving_company_name",255)->nullable()->after("delivery_date");

            $table->index("delivery_date", "order_details_del_date_index");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropIndex("order_details_del_date_index");
            $table->dropColumn('delivery_date');
            $table->dropColumn('receiving_company_name');
        });
    }
};
