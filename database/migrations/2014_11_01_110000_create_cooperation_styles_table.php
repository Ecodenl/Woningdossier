<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCooperationStylesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cooperation_styles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cooperation_id')->unsigned();
            $table->foreign('cooperation_id')->references('id')->on('cooperations');
            $table->string('logo_url')->nullable();
            $table->string('primary_color')->default('#30815f');
            $table->string('secundairy_color')->default('#27ae60');
            $table->string('tertiary_color')->default('#2980b9');
            $table->string('quaternary_color')->default('#8e44ad');
            $table->string('css_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cooperation_styles');
    }
}
