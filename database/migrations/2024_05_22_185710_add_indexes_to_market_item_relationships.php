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
        Schema::table('market_item_relationships', function (Blueprint $table) {
            $table->string('external_code')->nullable()->index()->change();
            $table->string('relationshipable_id')->index()->change();
            $table->string('relationshipable_type')->index()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('market_item_relationships', function (Blueprint $table) {
            $table->string('external_code')->nullable()->change();
            $table->string('relationshipable_id')->change();
            $table->string('relationshipable_type')->change();
        });
    }
};
