<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCooperationIdToBuildingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->unsignedInteger('cooperation_id')->nullable()->default(null)->after('user_id');
            $table->foreign('cooperation_id')->references('id')->on('cooperations')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->dropForeign('cooperation_id');
            $table->dropColumn('cooperation_id');
        });
    }
}
