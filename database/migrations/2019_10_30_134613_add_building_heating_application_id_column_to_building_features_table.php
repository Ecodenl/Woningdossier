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
        Schema::table('building_features', function (Blueprint $table) {
            $table->integer('building_heating_application_id')->unsigned()->nullable()->default(null)->after('input_source_id');
            $table->foreign('building_heating_application_id')->references('id')->on('building_heating_applications')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('building_features', function (Blueprint $table) {
            $table->dropForeign(['building_heating_application_id']);
            $table->dropColumn('building_heating_application_id');
        });
    }
};
