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
        Schema::create('user_interests', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');

            $table->integer('input_source_id')->unsigned()->nullable()->default(1);
            $table->foreign('input_source_id')->references('id')->on('input_sources')->onDelete('set null');

            $table->string('interested_in_type');
            $table->integer('interested_in_id')->unsigned();

            $table->integer('interest_id')->unsigned();
            $table->foreign('interest_id')->references('id')->on('interests')->onDelete('restrict');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_interests');
    }
};
