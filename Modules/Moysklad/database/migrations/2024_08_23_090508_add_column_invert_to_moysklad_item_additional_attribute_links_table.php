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
        Schema::table('moysklad_item_additional_attribute_links', function (Blueprint $table) {
            $table->boolean('invert')->nullable()->default(false)->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('moysklad_item_additional_attribute_links', function (Blueprint $table) {
            $table->dropColumn('invert');
        });
    }
};
