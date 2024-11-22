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
        Schema::create('building_types', function (Blueprint $table) {
            $table->increments('id');

            // NOTE: Building type categories table gets created later, so foreign is added on
            // the '2021_08_27_092617_create_building_type_categories_table' migration.
            $table->unsignedBigInteger('building_type_category_id')->nullable();
            //$table->foreign('building_type_category_id')->references('id')->on('building_type_categories')->onDelete('cascade');

            $table->json('name');
            $table->integer('calculate_value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('building_types');
    }
};
