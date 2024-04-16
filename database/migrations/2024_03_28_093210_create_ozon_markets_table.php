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
        Schema::create('ozon_markets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('client_id')->unique();
            $table->string('api_key')->unique();
            $table->unsignedInteger('min_price_percent')->nullable();
            $table->unsignedInteger('max_price_percent')->nullable();
            $table->unsignedInteger('seller_price_percent')->nullable();
            $table->boolean('open')->nullable()->default(false);
            $table->unsignedInteger('max_count')->nullable()->default(50);
            $table->unsignedInteger('min')->nullable()->default(2);
            $table->unsignedInteger('max')->nullable()->default(5);
            $table->boolean('seller_price')->nullable()->default(true);
            $table->float('acquiring')->nullable();
            $table->float('last_mile')->nullable();
            $table->float('max_mile')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ozon_markets');
    }
};
