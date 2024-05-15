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
            $table->uuid('ms_uuid')->nullable()->unique();
            $table->string('code')->index();
            $table->text('name')->nullable();
            $table->foreignUuid('supplier_id')->constrained('suppliers');
            $table->string('article')->nullable()->index();
            $table->string('brand')->nullable()->index();
            $table->double('price')->nullable()->default(0);
            $table->unsignedInteger('count')->nullable()->default(0);
            $table->unsignedInteger('multiplicity')->nullable()->default(1);
            $table->foreignId('user_id')->constrained('users');
            $table->boolean('updated')->nullable()->default(false);
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
