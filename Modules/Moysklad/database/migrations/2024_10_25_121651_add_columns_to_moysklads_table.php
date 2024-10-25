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
            $table->boolean('enabled_recount_retail_markup')->nullable()->default(false)->after('enabled_orders');
            $table->string('link_recount_retail_markup_percent')->nullable()->after('enabled_recount_retail_markup');
            $table->string('link_name_recount_retail_markup_percent')->nullable()->after('link_recount_retail_markup_percent');
            $table->string('link_label_recount_retail_markup_percent')->nullable()->after('link_name_recount_retail_markup_percent');
            $table->string('link_type_recount_retail_markup_percent')->nullable()->after('link_label_recount_retail_markup_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('moysklads', function (Blueprint $table) {
            $table->dropColumn([
                'enabled_recount_retail_markup',
                'link_recount_retail_markup_percent',
                'link_name_recount_retail_markup_percent',
                'link_label_recount_retail_markup_percent',
                'link_type_recount_retail_markup_percent',]);
        });
    }
};
