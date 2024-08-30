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
            $table->string('wbitemable_id')->change();
            $table->string('wbitemable_type')->default('App\\Models\\Item')->after('wbitemable_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wb_items', function (Blueprint $table) {
            $table->uuid('wbitemable_id')->change();
            $table->dropColumn('wbitemable_type');
        });
    }
};
