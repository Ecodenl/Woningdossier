<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('service_values', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('service_id')->unsigned()->nullable()->default(null);
            $table->foreign('service_id')->references('id')->on('services')->onDelete('restrict');

            $table->json('value');
            $table->integer('calculate_value')->unsigned()->nullable();
            $table->integer('order');
            $table->boolean('is_default')->default(false);
            $table->json('configurations')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('service_values');
    }
};
