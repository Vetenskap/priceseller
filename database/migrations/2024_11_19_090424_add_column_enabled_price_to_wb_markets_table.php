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
            $table->boolean('enabled_price')->nullable()->default(false)->after('minus_stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wb_markets', function (Blueprint $table) {
            $table->dropColumn('enabled_price');
        });
    }
};
