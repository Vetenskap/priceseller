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
        Schema::create('user_notification_actions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->boolean('enabled');
            $table->foreignUuid('notification_action_id')->constrained('notification_actions')->cascadeOnDelete();
            $table->foreignUuid('user_notification_id')->constrained('user_notifications')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_notification_actions');
    }
};
