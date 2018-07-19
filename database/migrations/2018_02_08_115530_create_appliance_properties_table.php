<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppliancePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appliance_properties', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('appliance_id')->unsigned()->nullable()->default(null);
            $table->foreign('appliance_id')->references('id')->on('appliances') ->onDelete('restrict');

            $table->string('name')->default('');
            $table->string('value')->default('');
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
        Schema::dropIfExists('appliance_properties');
    }
}
