<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserInterestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_interests', function (Blueprint $table) {
            $table->increments('id');

            $this->integer('user_id')->unsigned();
            $this->foreign('user_id')->references('id')->on('users')->onDelete('restrict');

            $this->enum('interested_in_type', ['service', 'element', 'measure_application']);
            $this->integer('interested_in_id')->unsigned();

            $this->integer('interest_id')->unsigned();
            $this->foreign('interest_id')->references('id')->on('interests')->onDelete('restrict');

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
        Schema::dropIfExists('user_interests');
    }
}
