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
        Schema::table('ozon_markets', function (Blueprint $table) {
            $table->renameColumn('min_price_percent', 'min_price_coefficient');
            $table->float('min_price_coefficient')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ozon_markets', function (Blueprint $table) {
            $table->renameColumn('min_price_coefficient', 'min_price_percent');
            $table->unsignedInteger('min_price_percent')->nullable()->change();
        });
    }
};
