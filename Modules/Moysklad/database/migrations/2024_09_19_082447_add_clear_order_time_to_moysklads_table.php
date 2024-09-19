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
        Schema::table('moysklads', function (Blueprint $table) {
            $table->unsignedBigInteger('clear_order_time')->nullable()->after('enabled_orders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('moysklads', function (Blueprint $table) {
            $table->dropColumn('clear_order_time');
        });
    }
};
