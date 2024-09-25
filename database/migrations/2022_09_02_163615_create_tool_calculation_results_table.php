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
    public function up()
    {
        Schema::create('tool_calculation_results', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->json('name');
            $table->json('help_text')->nullable();
            $table->string('short');
            $table->string('data_type')->default(Caster::STRING);
            $table->string('unit_of_measure')->nullable();
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
        Schema::dropIfExists('tool_calculation_results');
    }
};
