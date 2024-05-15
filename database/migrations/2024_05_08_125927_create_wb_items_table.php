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
        Schema::create('wb_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nm_id')->nullable()->unique();
            $table->string('vendor_code')->nullable()->index();
            $table->string('sku')->nullable()->unique();
            $table->unsignedInteger('sales_percent')->nullable();
            $table->unsignedInteger('min_price')->nullable();
            $table->double('retail_markup_percent')->nullable();
            $table->double('package')->nullable();
            $table->double('volume')->nullable();
            $table->unsignedInteger('price')->nullable();
            $table->unsignedInteger('price_market')->nullable();
            $table->unsignedInteger('count')->nullable();
            $table->foreignUuid('item_id')->constrained('items');
            $table->foreignUuid('wb_market_id')->constrained('wb_markets');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wb_items');
    }
};
