<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrivateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('private_messages', function (Blueprint $table) {
            $table->increments('id');

            $table->string('title')->nullable()->default(null);

            $table->string('request_type')->nullable();

            $table->text('message');

            $table->integer('from_user_id')->unsigned()->nullable()->default(null);
            $table->foreign('from_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('from_user_read')->default(false);

            $table->integer('to_user_id')->unsigned()->nullable()->default(null);
            $table->foreign('to_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('to_user_read')->default(false);

            $table->integer('from_cooperation_id')->unsigned()->nullable()->default(null);
            $table->foreign('from_cooperation_id')->references('id')->on('cooperations')->onDelete('cascade');

            $table->integer('to_cooperation_id')->unsigned()->nullable()->default(null);
            $table->foreign('to_cooperation_id')->references('id')->on('cooperations')->onDelete('cascade');

            $table->string('status')->nullable();

            $table->boolean('allow_access')->default(false);

            $table->boolean('is_completed')->default(false);

            $table->integer('main_message')->nullable()->default(null);

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
        Schema::dropIfExists('private_messages');
    }
}
