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
            $table->unsignedInteger('minus_stock')->nullable()->default(0)->after('max');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wb_markets', function (Blueprint $table) {
            $table->dropColumn('minus_stock');
        });
    }
};