<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_notes', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_id');
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('restrict');

            $table->text('note');

            $table->unsignedInteger('created_by_id')->nullable()->default(null);
            $table->unsignedInteger('updated_by_id')->nullable()->default(null);
            $table->foreign('created_by_id')
                ->references('id')->on('users')
                ->onDelete('restrict');
            $table->foreign('updated_by_id')
                ->references('id')->on('users')
                ->onDelete('restrict');

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
        Schema::dropIfExists('user_notes');
    }
}
