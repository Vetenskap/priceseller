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
        Schema::create('supplier_report_log_markets', function (Blueprint $table) {
            $table->id();
            $table->string('status')->nullable();
            $table->longText('message');
            $table->string('logable_id')->nullable();
            $table->string('logable_type')->nullable();
            $table->foreignUuid('task_log')->constrained('task_logs')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_report_log_markets');
    }
};
