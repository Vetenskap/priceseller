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
        Schema::create('ozon_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('product_id')->nullable()->unique();
            $table->string('offer_id')->nullable()->index();
            $table->unsignedInteger('min_price_percent')->nullable();
            $table->unsignedInteger('min_price')->nullable();
            $table->double('shipping_processing')->nullable();
            $table->double('direct_flow_trans')->nullable();
            $table->double('deliv_to_customer')->nullable();
            $table->unsignedInteger('sales_percent')->nullable();
            $table->unsignedInteger('price')->nullable();
            $table->unsignedInteger('price_seller')->nullable();
            $table->unsignedInteger('price_min')->nullable();
            $table->unsignedInteger('price_max')->nullable();
            $table->unsignedInteger('price_market')->nullable();
            $table->unsignedInteger('count')->nullable();
            $table->foreignUuid('item_id')->constrained('items');
            $table->foreignUuid('ozon_market_id')->constrained('ozon_markets');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ozon_items');
    }
};
