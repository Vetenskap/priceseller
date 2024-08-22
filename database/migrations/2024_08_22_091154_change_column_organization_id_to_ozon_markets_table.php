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
        Schema::table('ozon_markets', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);

            $table->foreign('organization_id')
                ->references('id')->on('organizations')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ozon_markets', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);

            $table->foreign('organization_id')
                ->references('id')->on('organizations');
        });
    }
};
