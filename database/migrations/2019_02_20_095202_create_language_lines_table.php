<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLanguageLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('language_lines', function (Blueprint $table) {
            $table->increments('id');
            $table->string('group');
            $table->index('group');
            $table->string('key');
            $table->text('text');

            $table->integer('step_id')->nullable()->unsigned();
            $table->foreign('step_id')->references('id')->on('steps')->onDelete('set null');

            $table->integer('main_language_line_id')->nullable()->unsigned();
            $table->foreign('main_language_line_id')->references('id')->on('language_lines')->onDelete('cascade');

            $table->integer('help_language_line_id')->nullable()->unsigned();
            $table->foreign('help_language_line_id')->references('id')->on('language_lines')->onDelete('cascade');

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
        Schema::drop('language_lines');
    }
}
