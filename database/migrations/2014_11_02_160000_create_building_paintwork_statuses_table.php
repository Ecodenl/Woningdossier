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
        Schema::create('building_paintwork_statuses', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('building_id')->unsigned();
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('restrict');

            $table->integer('last_painted_year')->unsigned();

            $table->integer('paintwork_status_id')->unsigned()->nullable();
            $table->foreign('paintwork_status_id')->references('id')->on('paintwork_statuses')->onDelete('restrict');

            $table->integer('wood_rot_status_id')->unsigned()->nullable();
            $table->foreign('wood_rot_status_id')->references('id')->on('wood_rot_statuses')->onDelete('restrict');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('building_paintwork_statuses');
    }
};
