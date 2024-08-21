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
        Schema::create('moysklad_webhook_reports', function (Blueprint $table) {
            $table->id();
            $table->boolean('status');
            $table->foreignUuid('moysklad_webhook_id')->constrained('moysklad_webhooks')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moysklad_webhook_reports');
    }
};
