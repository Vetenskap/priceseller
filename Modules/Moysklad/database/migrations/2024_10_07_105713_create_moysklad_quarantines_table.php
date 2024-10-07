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
        Schema::create('moysklad_quarantines', function (Blueprint $table) {
            $table->id();
            $table->float('supplier_buy_price');
            $table->foreignId('moysklad_id')->constrained('moysklads')->cascadeOnDelete();
            $table->foreignUuid('item_id')->constrained('items')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moysklad_quarantines');
    }
};
