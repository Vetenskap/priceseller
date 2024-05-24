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
        Schema::create('items_export_reports', function (Blueprint $table) {
            $table->id();
            $table->uuid()->nullable();
            $table->string('message');
            $table->tinyInteger('status');
            $table->string('reportable_id');
            $table->string('reportable_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items_export_reports');
    }
};
