<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cooperation_measure_applications', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->json('name');
            $table->json('info');

            $table->json('costs');
            $table->decimal('savings_money');

            $table->json('extra');
            $table->boolean('is_extensive_measure')->default(false);
            $table->boolean('is_deletable')->default(false);

            $table->unsignedInteger('cooperation_id');
            $table->foreign('cooperation_id')->references('id')->on('cooperations')->onDelete('cascade');
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
        Schema::dropIfExists('cooperation_measure_applications');
    }
};
