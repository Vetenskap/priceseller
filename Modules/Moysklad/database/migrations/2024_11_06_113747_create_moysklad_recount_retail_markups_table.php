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
        Schema::create('moysklad_recount_retail_markups', function (Blueprint $table) {
            $table->id();
            $table->boolean('enabled')->nullable()->default(false);
            $table->string('link')->nullable();
            $table->string('link_name')->nullable();
            $table->string('link_label')->nullable();
            $table->string('link_type')->nullable();
            $table->uuid('price_type_uuid')->nullable();
            $table->foreignId('moysklad_id')->constrained('moysklads')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moysklad_recount_retail_markups');
    }
};
