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
        Schema::create('moysklad_bundle_api_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->tinyInteger('status');
            $table->string('message');
            $table->unsignedBigInteger('updated');
            $table->unsignedBigInteger('created');
            $table->unsignedBigInteger('errors');
            $table->foreignId('moysklad_id')->constrained('moysklads')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moysklad_bundle_api_reports');
    }
};
