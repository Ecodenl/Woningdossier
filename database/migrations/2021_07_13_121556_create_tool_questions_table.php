<?php

use App\Helpers\DataTypes\Caster;
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
        Schema::create('tool_questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('short')->nullable()->default(null);
            $table->string('save_in')->nullable()->default(null);
            $table->unsignedInteger('for_specific_input_source_id')->nullable();
            $table->foreign('for_specific_input_source_id')->references('id')->on('input_sources')->onDelete('set null');
            $table->json('name');
            $table->json('help_text');
            $table->json('placeholder')->nullable()->default(null);
            $table->string('data_type')->default(Caster::STRING);
            $table->boolean('coach')->default(true);
            $table->boolean('resident')->default(true);
            $table->json('options')->nullable()->default(null);
            $table->json('validation')->nullable()->default(null);
            $table->string('unit_of_measure')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tool_questions');
    }
};
