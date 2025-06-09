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
            $table->string('customer_notes', 4000)->change();
            $table->string('supplier_notes', 4000)->change();
            $table->string('qty_detail',4000)->after('qty')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->string('customer_notes', 255)->change();
            $table->string('supplier_notes', 255)->change();
            $table->dropColumn('qty_detail');
        });
    }
};
