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
        //number_of_boxes
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('number_of_boxes')->default(0)->after('delivery_instructions');
            $table->integer('delivery_run_position')->default(1)->after('delivery_run');
            $table->string('picking_instructions', 4000)->nullable()->after('delivery_instructions');
            $table->boolean('is_credit_note')->default(false)->after('parent_order_id');
            $table->string('freight_rule', 128)->nullable()->comment("require_freight")->after('is_credit_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('number_of_boxes');
            $table->dropColumn('delivery_run_position');
            $table->dropColumn('picking_instructions');
            $table->dropColumn('is_credit_note');
            $table->dropColumn('freight_rule');
        });
    }
};
