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
        Schema::table('ozon_items', function (Blueprint $table) {
            $table->string('ozonitemable_id')->change();
            $table->string('ozonitemable_type')->default('App\Models\Item')->after('ozonitemable_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ozon_items', function (Blueprint $table) {
            $table->uuid('ozonitemable_id')->change();
            $table->dropColumn('ozonitemable_type');
        });
    }
};
