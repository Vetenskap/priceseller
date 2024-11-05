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
            $table->unsignedInteger('subject_id')->nullable()->after('sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wb_items', function (Blueprint $table) {
            $table->dropColumn('subject_id');
        });
    }
};
