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
        Schema::create('samson_api_item_additional_attribute_links', function (Blueprint $table) {
            $table->id();
            $table->longText('link')->nullable();
            $table->foreignUuid('item_attribute_id')->constrained('item_attributes', indexName: "sa_item_add_attr_links_item_attr_id_foreign")->cascadeOnDelete();
            $table->foreignId('samson_api_id')->constrained('samson_apis')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('samson_api_item_additional_attribute_links');
    }
};
