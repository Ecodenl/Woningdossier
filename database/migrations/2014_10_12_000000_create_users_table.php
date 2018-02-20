<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');

            $table->string('first_name')->default('');
            $table->integer('last_name_prefix_id')->unsigned()->nullable()->default(null);
            $table->foreign('last_name_prefix_id')->references('id')->on('last_name_prefixes') ->onDelete('restrict');
            $table->string('last_name')->default('');

            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->integer('title_id')->unsigned()->nullable()->default(null);
            $table->foreign('title_id')->references('id')->on('titles') ->onDelete('restrict');

            $table->string('phone_number')->default('');
            $table->string('mobile')->default('');
            $table->string('occupation')->default('');

            $table->dateTime('last_visit')->nullable()->default(null);
            $table->integer('visit_count')->default(0);

            $table->boolean('active')->default(1);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
