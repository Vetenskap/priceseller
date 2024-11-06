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
            $table->json('price_type_uuids')->nullable()->after('link_type_recount_retail_markup_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('moysklads', function (Blueprint $table) {
            $table->dropColumn('price_type_uuids');
        });
    }
};
