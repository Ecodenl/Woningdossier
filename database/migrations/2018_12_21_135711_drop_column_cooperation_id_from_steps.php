<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnCooperationIdFromSteps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('steps', function (Blueprint $table) {
            $table->dropForeign('steps_cooperation_id_foreign');
            $table->dropColumn('cooperation_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('steps', function (Blueprint $table) {
            $table->integer('cooperation_id')->unsigned()->nullable()->default(null);
            $table->foreign('cooperation_id')->references('id')->on('cooperations')->onDelete('restrict');
        });
    }
}
