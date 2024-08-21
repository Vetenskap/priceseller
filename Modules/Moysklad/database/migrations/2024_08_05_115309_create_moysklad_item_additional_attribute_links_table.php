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
        Schema::create('moysklad_item_additional_attribute_links', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('item_attribute_id')->constrained('item_attributes', indexName: "ms_item_add_attr_links_item_attr_id_foreign")->cascadeOnDelete();
            $table->longText('link');
            $table->string('link_name');
            $table->string('link_label');
            $table->string('type');
            $table->string('user_type');
            $table->foreignId('moysklad_id')->constrained('moysklads')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moysklad_item_additional_attribute_links');
    }
};
