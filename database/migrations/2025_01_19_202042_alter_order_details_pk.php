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
            $table->integer('idx')->default(1)->after('prd_code');
            // $table->dropPrimary(['order_number', 'prd_code']);
            $table->primary(['order_number', 'prd_code', 'idx']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropPrimary(['order_number', 'prd_code', 'idx']);
            $table->dropColumn('idx');
            $table->primary(['order_number', 'prd_code']);
        });
    }
};
