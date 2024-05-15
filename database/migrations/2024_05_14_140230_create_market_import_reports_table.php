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
        Schema::create('market_import_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('correct')->nullable()->default(0);
            $table->unsignedInteger('error')->nullable()->default(0);
            $table->string('message');
            $table->tinyInteger('status');
            $table->uuid()->nullable();
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
        Schema::dropIfExists('market_import_reports');
    }
};
