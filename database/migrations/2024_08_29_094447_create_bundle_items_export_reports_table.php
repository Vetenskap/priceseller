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
        Schema::create('bundle_items_export_reports', function (Blueprint $table) {
            $table->id();
            $table->uuid()->nullable();
            $table->string('message');
            $table->tinyInteger('status');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bundle_items_export_reports');
    }
};