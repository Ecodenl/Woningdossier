<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDossierSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dossier_settings', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('input_source_id')->nullable()->default(null);
            $table->foreign('input_source_id')->references('id')->on('input_sources')->nullOnDelete();

            $table->unsignedInteger('building_id')->unsigned();
            $table->foreign('building_id')->references('id')->on('buildings')->cascadeOnDelete();

            $table->string('type');

            $table->timestamp('done_at')->nullable()->default(null);

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
        Schema::dropIfExists('dossier_settings');
    }
}
