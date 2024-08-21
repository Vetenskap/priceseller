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
        Schema::create('moysklad_item_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orders');
            $table->boolean('new')->nullable()->default(true);
            $table->foreignUuid('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('moysklad_id')->constrained('moysklads')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moysklad_item_orders');
    }
};
