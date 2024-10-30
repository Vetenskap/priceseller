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
        Schema::create('email_supplier_warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('value')->nullable();
            $table->foreignId('email_supplier_id')->constrained('email_suppliers')->cascadeOnDelete();
            $table->foreignUuid('supplier_warehouse_id')->constrained('supplier_warehouses')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_supplier_warehouses');
    }
};
