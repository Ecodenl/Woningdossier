<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('people', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users') ->onDelete('restrict');

            $table->string('first_name')->default('');
            $table->string('last_name')->default('');

            $table->integer('organisation_id')->nullable()->unsigned()->default(null);
            $table->foreign('organisation_id')->references('id')->on('organisations') ->onDelete('restrict');

            $table->integer('last_name_prefix_id')->nullable()->unsigned()->default(null);
            $table->foreign('last_name_prefix_id')->references('id')->on('last_name_prefixes') ->onDelete('restrict');

            $table->integer('type_id')->nullable()->unsigned()->default(null);
            $table->foreign('type_id')->references('id')->on('person_types') ->onDelete('restrict');

            $table->integer('title_id')->unsigned()->nullable()->default(null);
            $table->foreign('title_id')->references('id')->on('titles') ->onDelete('restrict');

            $table->date('date_of_birth')->nullable()->default(null);
            $table->string('first_name_partner')->default('');
            $table->string('last_name_partner')->default('');
            $table->date('date_of_birth_partner')->nullable()->default(null);

            $table->boolean('primary')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('people');
    }
}
