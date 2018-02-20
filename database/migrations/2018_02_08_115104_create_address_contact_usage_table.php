<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressContactUsageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('address_contact_usage', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('address_id')->unsigned()->nullable()->default(null);
            $table->foreign('address_id')->references('id')->on('addresses') ->onDelete('restrict');

            $table->integer('contact_id')->unsigned()->nullable()->default(null);
            $table->foreign('contact_id')->references('id')->on('contacts') ->onDelete('restrict');

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
        Schema::dropIfExists('address_contact_usage');
    }
}
