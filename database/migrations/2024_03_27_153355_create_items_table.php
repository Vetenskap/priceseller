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
        Schema::create('items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ms_uuid')->nullable()->index('ms_uuid_index');
            $table->string('code')->unique();
            $table->foreignUuid('supplier_id')->constrained('suppliers');
            $table->string('article_supplier')->nullable()->index('article_supplier_index');
            $table->string('brand')->nullable()->index('brand_index');
            $table->string('article_manufacture')->nullable()->index('article_manufacture_index');
            $table->double('price')->nullable()->default(0);
            $table->unsignedInteger('count')->nullable()->default(0);
            $table->unsignedInteger('multiplicity')->nullable()->default(1);
            $table->foreignId('user_id')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
