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
        Schema::create('scans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->json('name');
            $table->json('slug');
            $table->string('short');
            $table->timestamps();
        });

        Schema::table('steps', function (Blueprint $table) {
            $table->foreign('scan_id')->references('id')->on('scans');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('steps', function (Blueprint $table) {
            $table->dropForeign(['scan_id']);
        });
        Schema::dropIfExists('scans');
    }
};
