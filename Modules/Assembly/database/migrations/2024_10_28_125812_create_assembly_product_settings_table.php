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
        Schema::create('assembly_product_settings', function (Blueprint $table) {
            $table->id();
            $table->string('field');
            $table->string('label')->nullable();
            $table->string('type');
            $table->boolean('additional')->nullable()->default(false);
            $table->string('market');
            $table->string('color')->nullable();
            $table->string('size_level');
            $table->unsignedInteger('index')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assembly_product_settings');
    }
};
