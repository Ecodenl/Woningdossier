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
     */
    public function down(): void
    {
        Schema::dropIfExists('cooperation_styles');
    }
};
