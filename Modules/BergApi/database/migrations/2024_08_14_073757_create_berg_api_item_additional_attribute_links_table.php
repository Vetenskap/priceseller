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
        Schema::create('berg_api_item_additional_attribute_links', function (Blueprint $table) {
            $table->id();
            $table->longText('link')->nullable();
            $table->string('link_label')->nullable();
            $table->foreignUuid('item_attribute_id')->constrained('item_attributes', indexName: "ba_item_add_attr_links_item_attr_id_foreign")->cascadeOnDelete();
            $table->foreignId('berg_api_id')->constrained('berg_apis')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berg_api_item_additional_attribute_links');
    }
};
