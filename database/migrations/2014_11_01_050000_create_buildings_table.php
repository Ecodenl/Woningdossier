<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buildings', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');

            $table->unsignedBigInteger('municipality_id')->nullable()->default(null);
            $table->foreign('municipality_id')->references('id')->on('municipalities')->nullOnDelete();

            $table->string('street')->default('');
            $table->string('number')->default('');
            $table->string('extension')->default('');
            $table->string('city')->default('');
            $table->string('postal_code')->default('');
            $table->string('country_code', 2)->default('nl');

            $table->boolean('owner')->unsigned()->nullable();

            $table->boolean('primary')->default(false);

            $table->string('bag_addressid')->default('');
            $table->string('bag_woonplaats_id')->nullable()->default(null);

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
        Schema::dropIfExists('buildings');
    }
}
