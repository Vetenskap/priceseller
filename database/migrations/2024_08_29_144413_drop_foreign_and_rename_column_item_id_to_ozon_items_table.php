<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ozon_items', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->renameColumn('item_id', 'ozonitemable_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ozon_items', function (Blueprint $table) {
            $table->renameColumn('ozonitemable_id', 'item_id');
            $table->foreign('item_id')
                ->references('id')->on('items')->cascadeOnDelete();
        });
    }
};
