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
        Schema::table('berg_api_warehouses', function (Blueprint $table) {
            $table->renameColumn('warehouse_id', 'warehouse_name');
            $table->string('warehouse_name')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('berg_api_warehouses', function (Blueprint $table) {
            $table->renameColumn('warehouse_name', 'warehouse_id');
            $table->integer('warehouse_id')->change();
        });
    }
};
