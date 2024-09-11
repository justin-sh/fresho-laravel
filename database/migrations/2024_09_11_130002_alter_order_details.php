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
            $table->dropPrimary(['order_id', 'prd_code']);
            $table->dropColumn('order_id');
            $table->string('order_number', 255)->first();
            $table->primary(['order_number', 'prd_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropPrimary(['order_number', 'prd_code']);
            $table->uuid('order_id')->first();
            $table->dropColumn('order_number');
            $table->primary(['order_id', 'prd_code']);
        });
    }
};
