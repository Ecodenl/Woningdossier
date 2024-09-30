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
        Schema::create('sub_steps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->json('name');
            $table->json('slug');
            $table->integer('order');
            $table->json('conditions')->nullable()->default(null);
            $table->unsignedInteger('step_id');
            $table->foreign('step_id')->references('id')->on('steps')->onDelete('cascade');

            $table->unsignedBigInteger('sub_step_template_id');
            $table->foreign('sub_step_template_id')->references('id')->on('sub_step_templates')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_steps');
    }
};
