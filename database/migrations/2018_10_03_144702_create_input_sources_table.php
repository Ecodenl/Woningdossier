<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInputSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('input_sources', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('short');
            $table->integer('order');
            $table->timestamps();
        });

        $inputSources = [
            [
                'name' => 'Bewoner',
                'short' => 'resident',
                'order' => 1,
            ],
            [
                'name' => 'Voorbeeld woning',
                'short' => 'example-building',
                'order' => 2,
            ],
            [
                'name' => 'Coach',
                'short' => 'coach',
                'order' => 3,
            ],
            [
                'name' => 'Coöperatie',
                'short' => 'cooperation',
                'order' => 4,
            ],
        ];

        DB::table('input_sources')->insert($inputSources);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('input_sources');
    }
}
