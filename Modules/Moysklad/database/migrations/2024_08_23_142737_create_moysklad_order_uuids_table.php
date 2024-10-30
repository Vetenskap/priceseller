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
        Schema::create('moysklad_order_uuids', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->foreignId('moysklad_id')->constrained('moysklads')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moysklad_order_uuids');
    }
};
