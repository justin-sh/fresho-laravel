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
        Schema::table('fresho_products', function (Blueprint $table){
            $table->string('hoc_name', 128)->nullable()->after('hoc_code');
            $table->float('unit_map_ratio')->default(1.0)->after('hoc_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fresho_products', function (Blueprint $table){
            $table->dropColumn('hoc_name');
            $table->dropColumn('unit_map_ratio');
        });
    }
};
