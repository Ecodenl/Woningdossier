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
        Schema::create('private_messages', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('building_id')->unsigned()->nullable();
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('set null');

            $table->boolean('is_public')->nullable();
            $table->string('from_user')->default('');
            $table->text('message');

            $table->integer('from_user_id')->unsigned()->nullable();
            $table->foreign('from_user_id')->references('id')->on('users')->onDelete('set null');

            $table->integer('from_cooperation_id')->unsigned()->nullable();
            $table->foreign('from_cooperation_id')->references('id')->on('cooperations')->onDelete('cascade');

            $table->integer('to_cooperation_id')->unsigned()->nullable();
            $table->foreign('to_cooperation_id')->references('id')->on('cooperations')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('private_messages');
    }
};
