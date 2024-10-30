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
            $table->json('payload')->after('status');
            $table->longText('exception')->nullable()->after('payload');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('moysklad_webhook_reports', function (Blueprint $table) {
            $table->dropColumn(['payload', 'exception']);
        });
    }
};
