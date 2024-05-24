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
        Schema::create('email_price_items', function (Blueprint $table) {
            $table->id();
            $table->string('article')->index();
            $table->string('brand')->index();
            $table->string('price');
            $table->string('stock');
            $table->string('message');
            $table->tinyInteger('status');
            $table->foreignUuid('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignUuid('item_id')->nullable()->constrained('items')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_price_items');
    }
};
