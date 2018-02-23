<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressUserUsageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('address_user_usages', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('address_id')->unsigned()->nullable()->default(null);
            $table->foreign('address_id')->references('id')->on('addresses') ->onDelete('restrict');

            $table->integer('user_id')->unsigned()->nullable()->default(null);
            $table->foreign('user_id')->references('id')->on('users') ->onDelete('restrict');

            $table->integer('usage_percentage')->nullable()->default(null);

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

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
        Schema::dropIfExists('address_user_usages');
    }
}
