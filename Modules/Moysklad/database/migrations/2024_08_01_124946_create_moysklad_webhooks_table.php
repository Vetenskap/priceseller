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
        Schema::create('moysklad_webhooks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->string('action');
            $table->uuid('moysklad_webhook_uuid')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moysklad_webhooks');
    }
};
