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
        Schema::create('moysklad_webhook_report_events', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->nullable()->default(true);
            $table->json('event');
            $table->string('message');
            $table->json('exception')->nullable();
            $table->string('itemable_id')->nullable();
            $table->string('itemable_type')->nullable();
            $table->foreignId('moysklad_webhook_report_id')->constrained('moysklad_webhook_reports', indexName: "ms_webhook_report_events_webhook_report_id_foreign")->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moysklad_webhook_report_events');
    }
};
