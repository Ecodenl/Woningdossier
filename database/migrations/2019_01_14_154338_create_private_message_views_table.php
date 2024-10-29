<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('private_message_views', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('private_message_id')->unsigned();
            $table->foreign('private_message_id')->references('id')->on('private_messages')->onDelete('cascade');

            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->integer('cooperation_id')->unsigned()->nullable();
            $table->foreign('cooperation_id')->references('id')->on('cooperations')->onDelete('cascade');

            $table->dateTime('read_at')->nullable()->default(null);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('private_message_views');
    }
};
