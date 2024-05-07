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
            $table->integer('header_start')->nullable();
            $table->string('header_article_supplier')->nullable();
            $table->string('header_article_manufacturer')->nullable();
            $table->string('header_brand')->nullable();
            $table->string('header_price')->nullable();
            $table->string('header_count')->nullable();
            $table->foreignUuid('email_id')->constrained('emails');
            $table->foreignUuid('supplier_id')->constrained('suppliers');
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
