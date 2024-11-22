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
        Schema::table('moysklad_webhook_reports', function (Blueprint $table) {
            $table->string('action')->nullable()->after('exception');
            $table->string('itemable_id')->nullable()->after('action');
            $table->string('itemable_type')->nullable()->after('itemable_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('moysklad_webhook_reports', function (Blueprint $table) {
            $table->dropColumn(['action', 'itemable_id', 'itemable_type']);
        });
    }
};
