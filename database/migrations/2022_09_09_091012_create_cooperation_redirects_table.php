<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCooperationRedirectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cooperation_redirects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('from_slug');
            $table->unsignedInteger('cooperation_id');
            $table->foreign('cooperation_id')->references('id')->on('cooperations')->onDelete('cascade');
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
        Schema::dropIfExists('cooperation_redirects');
    }
}
