<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCooperationScansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cooperation_scans', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedInteger('cooperation_id');
            $table->foreign('cooperation_id')->references('id')->on('cooperations')->onDelete('cascade');

            $table->unsignedBigInteger('scan_id');
            $table->foreign('scan_id')->references('id')->on('scans')->onDelete('cascade');
            
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
        Schema::dropIfExists('cooperation_scans');
    }
}
