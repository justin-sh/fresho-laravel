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
        Schema::table('fresho_products', function (Blueprint $table) {
            $table->string('qty_type_id',36)->nullable()->after('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fresho_products', function (Blueprint $table) {
            $table->dropColumn('qty_type_id');
        });
    }
};
