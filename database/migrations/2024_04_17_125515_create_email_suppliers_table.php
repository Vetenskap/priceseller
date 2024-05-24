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
        Schema::create('email_suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->string('filename')->nullable();
            $table->unsignedInteger('header_article')->nullable();
            $table->unsignedInteger('header_brand')->nullable();
            $table->unsignedInteger('header_price')->nullable();
            $table->unsignedInteger('header_count')->nullable();
            $table->foreignUuid('email_id')->constrained('emails')->cascadeOnDelete();
            $table->foreignUuid('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_suppliers');
    }
};
