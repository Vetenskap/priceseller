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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->string('orderable_id');
            $table->string('orderable_type');
            $table->unsignedBigInteger('count');
            $table->string('state')->default('new');
            $table->float('price');
            $table->string('currency_code')->nullable();
            $table->uuid('organization_id');
            $table->boolean('write_off')->nullable()->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
