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
        Schema::table('email_suppliers', function (Blueprint $table) {
            $table->unsignedInteger('header_warehouse')->nullable()->after('header_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_suppliers', function (Blueprint $table) {
            $table->dropColumn('header_warehouse');
        });
    }
};