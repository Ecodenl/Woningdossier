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
        Schema::create('building_type_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('short');
            $table->json('name');
            $table->timestamps();
        });

        Schema::table('building_types', function (Blueprint $table) {
            $table->foreign('building_type_category_id')->references('id')->on('building_type_categories')->onDelete('cascade');
        });

        Schema::table('building_features', function (Blueprint $table) {
            $table->foreign('building_type_category_id')->references('id')->on('building_type_categories')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('building_types', function (Blueprint $table) {
            $table->dropForeign(['building_type_category_id']);
        });
        Schema::table('building_features', function (Blueprint $table) {
            $table->dropForeign(['building_type_category_id']);
        });
        Schema::dropIfExists('building_type_categories');
    }
};
