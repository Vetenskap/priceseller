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
        Schema::table('email_price_items', function (Blueprint $table) {
            $table->string('article')->nullable()->index()->change();
            $table->string('brand')->nullable()->index()->change();
            $table->string('price')->nullable()->change();
            $table->string('stock')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_price_items', function (Blueprint $table) {
            $table->string('article')->index()->change();
            $table->string('brand')->index()->change();
            $table->string('price')->change();
            $table->string('stock')->change();
        });
    }
};
