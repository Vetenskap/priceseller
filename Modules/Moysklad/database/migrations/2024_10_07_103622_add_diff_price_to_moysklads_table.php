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
            $table->integer('diff_price')->nullable()->default(20)->after('clear_order_time');
            $table->boolean('enabled_diff_price')->nullable()->default(false)->after('diff_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('moysklads', function (Blueprint $table) {
            $table->dropColumn(['diff_price', 'enabled_diff_price']);
        });
    }
};
