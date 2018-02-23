<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrganisationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organisations', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users') ->onDelete('restrict');
            $table->integer('type_id')->nullable()->unsigned();
            $table->foreign('type_id')->references('id')->on('organisation_types') ->onDelete('restrict');
            $table->string('name')->default('');
            $table->string('website')->default('');
            $table->string('chamber_of_commerce_number')->default('');
            $table->string('vat_number')->default('');

            $table->integer('industry_id')->unsigned()->nullable()->default(null);
            $table->foreign('industry_id')->references('id')->on('industries') ->onDelete('restrict');
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
        Schema::dropIfExists('organisations');
    }
}
