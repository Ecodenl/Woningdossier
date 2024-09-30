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
        Schema::create('measure_applications', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('measure_type', ['energy_saving', 'maintenance']);
            $table->json('measure_name');
            $table->json('measure_info')->nullable();
            $table->string('short');
            $table->enum('application', ['place', 'replace', 'remove', 'repair']);
            $table->json('cost_range')->nullable();
            $table->decimal('savings_money')->nullable();
            $table->double('costs', 8, 2);
            $table->json('cost_unit');
            $table->double('minimal_costs', 8, 2);
            $table->integer('maintenance_interval');
            $table->json('maintenance_unit');

            $table->integer('step_id')->unsigned();
            $table->foreign('step_id')->references('id')->on('steps')->onDelete('restrict');

            $table->boolean('has_calculations')->default(true);
            $table->json('configurations')->nullable();
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
        Schema::dropIfExists('measure_applications');
    }
};
