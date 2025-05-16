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
        Schema::dropIfExists('tool_question_valuables');

        Schema::create('tool_question_valuables', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('tool_question_id');
            $table->foreign('tool_question_id')->references('id')->on('tool_questions')->onDelete('cascade');
            $table->boolean('show')->default(true);
            $table->integer('order');

            $table->unsignedBigInteger('tool_question_valuable_id')->index();
            $table->string('tool_question_valuable_type')->index();
            $table->json('extra')->nullable();
            $table->json('conditions')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tool_question_valuables');
    }
};
