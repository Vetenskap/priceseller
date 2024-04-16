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
        Schema::create('wb_markets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('api_key')->unique();
            $table->float('coefficient')->nullable()->default(1.0);
            $table->unsignedInteger('basic_logistics')->nullable();
            $table->unsignedInteger('price_one_liter')->nullable();
            $table->boolean('open')->nullable()->default(false);
            $table->unsignedInteger('max_count')->nullable()->default(50);
            $table->unsignedInteger('min')->nullable()->default(2);
            $table->unsignedInteger('max')->nullable()->default(5);
            $table->unsignedInteger('volume')->nullable();
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
        Schema::dropIfExists('wb_markets');
    }
};
