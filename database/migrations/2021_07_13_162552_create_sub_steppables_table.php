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
    public function up(): void
    {
        if ( ! Schema::hasTable('sub_steppables')) {
            Schema::create('sub_steppables', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('order');

                $table->unsignedBigInteger('sub_step_id');
                $table->foreign('sub_step_id')->references('id')->on('sub_steps')->onDelete('cascade');

                $table->unsignedBigInteger('sub_steppable_id');
                $table->string("sub_steppable_type")->nullable();
                $table->index(["sub_steppable_type", "sub_steppable_id"]);

                $table->unsignedBigInteger('tool_question_type_id')->nullable();
                $table->foreign('tool_question_type_id')->references('id')->on('tool_question_types')->onDelete('cascade');

                $table->json('conditions')->nullable()->default(null);
                $table->string('size')->nullable()->default(null);

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_step_tool_questions');
    }
};
