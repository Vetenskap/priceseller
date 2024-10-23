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
        Schema::table('wb_markets', function (Blueprint $table) {
            $table->boolean('enabled_update_commissions_in_time')->nullable()->default(false)->after('close');
            $table->string('update_commissions_time')->nullable()->after('enabled_update_commissions_in_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wb_markets', function (Blueprint $table) {
            $table->dropColumn(['enabled_update_commissions_in_time', 'update_commissions_time']);
        });
    }
};
