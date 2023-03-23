<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntegrationProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('integration_processes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Integration::class, 'integration_id');
            $table->foreignIdFor(\App\Models\Building::class, 'building_id');
            $table->string('process');
            $table->dateTime('synced_at')->nullable()->default(null);
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
        Schema::dropIfExists('integration_processes');
    }
}
