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
        Schema::table('items_import_reports', function (Blueprint $table) {
            $table->unsignedInteger('updated')->nullable()->default(0)->after('error');
            $table->unsignedInteger('deleted')->nullable()->default(0)->after('updated');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items_import_reports', function (Blueprint $table) {
            $table->dropColumn('updated');
            $table->dropColumn('deleted');
        });
    }
};
