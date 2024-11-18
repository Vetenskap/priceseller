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
        Schema::create('assembly_wb_supplies', function (Blueprint $table) {
            $table->id();
            $table->string('id_supply');
            $table->string('name');
            $table->string('closed_at')->nullable();
            $table->string('scan_dt')->nullable();
            $table->integer('cargo_type');
            $table->unsignedInteger('count_orders')->nullable();
            $table->boolean('done');
            $table->foreignUuid('wb_market_id')->constrained('wb_markets')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assembly_wb_supplies');
    }
};
