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
        Schema::create('pv_panel_yields', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('angle')->unsigned();

            $table->integer('pv_panel_orientation_id')->unsigned();
            $table->foreign('pv_panel_orientation_id')->references('id')->on('pv_panel_orientations')->onDelete('restrict');

            $table->decimal('yield', 3, 2);
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
        Schema::dropIfExists('pv_panel_yields');
    }
};
