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
        Schema::create('market_item_relationships', function (Blueprint $table) {
            $table->id();
            $table->string('external_code')->nullable();
            $table->string('code')->nullable();
            $table->string('message');
            $table->tinyInteger('status');
            $table->string('relationshipable_id');
            $table->string('relationshipable_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_item_relationships');
    }
};
