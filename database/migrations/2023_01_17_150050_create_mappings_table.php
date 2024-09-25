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
    public function up()
    {
        Schema::create('mappings', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable()->default(null);
            $table->json('conditions')->nullable()->default(null);

            $table->nullableMorphs('from_model');
            $table->string('from_value')->nullable()->default(null);

            $table->nullableMorphs('target_model');
            $table->string('target_value')->nullable()->default(null);
            $table->json('target_data')->nullable()->default(null);

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
        Schema::dropIfExists('mappings');
    }
};
