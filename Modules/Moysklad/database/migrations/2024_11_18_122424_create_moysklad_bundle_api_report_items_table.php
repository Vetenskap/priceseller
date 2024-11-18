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
        Schema::create('moysklad_bundle_api_report_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->tinyInteger('status');
            $table->string('message');
            $table->json('data');
            $table->json('exception');
            $table->foreignUuid('moysklad_bundle_api_report_id')->constrained('moysklad_bundle_api_reports', indexName: 'ms_bundle_api_report_items_bundle_api_report_id_foreign')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moysklad_bundle_api_report_items');
    }
};
