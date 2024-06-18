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
        Schema::create('moysklad_warehouse_stocks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('count');
            $table->foreignUuid('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('moysklad_warehouse_id')->constrained('moysklad_warehouses')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moysklad_warehouse_stocks');
    }
};
