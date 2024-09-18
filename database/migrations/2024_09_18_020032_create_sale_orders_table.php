<?php

use App\SalesStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sale_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title', 128)->unique();
            $table->date('pickup_at');
            $table->integer('qty')->default(0);
            $table->enum('state', array_column(SalesStatus::cases(), 'value'));
            $table->uuid('wh_id');
            $table->timestamps();
        });

        Schema::create('sale_order_details', function (Blueprint $table) {
//            $table->uuid('id')->primary();
            $table->uuid('so_id');
            $table->uuid('prd_id');
            $table->integer('qty')->default(0);
            $table->string('location', 32);
            $table->string('comment', 128)->nullable();
            $table->timestamps();
            $table->primary(['so_id', 'prd_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_order_details');
        Schema::dropIfExists('sale_orders');
    }
};
