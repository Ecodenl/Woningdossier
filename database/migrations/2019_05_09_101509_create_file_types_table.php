<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_types', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('file_type_category_id');
            $table->foreign('file_type_category_id')->references('id')->on('file_type_categories')->onDelete('cascade');

            $table->string('name');
            $table->string('short');

            $table->dateTime('duration')->nullable()->default(null);
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
        Schema::dropIfExists('file_types');
    }
}
