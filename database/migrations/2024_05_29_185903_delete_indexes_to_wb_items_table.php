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
        Schema::table('wb_items', function (Blueprint $table) {
            $table->dropUnique('wb_items_nm_id_unique');
            $table->dropUnique('wb_items_sku_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wb_items', function (Blueprint $table) {
            $table->string('nm_id')->nullable()->unique()->change();
            $table->string('sku')->nullable()->unique()->change();
        });
    }
};
