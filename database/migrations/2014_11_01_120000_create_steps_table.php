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
        Schema::create('steps', function (Blueprint $table) {
            $table->increments('id');

            // TODO: This can 99.99% be removed, after we drop the old general-data steps...
            $table->unsignedInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('steps')->onDelete('cascade');

            // NOTE: Scans table gets created later, so foreign is added on
            // the '2022_08_10_142501_create_scans_table' migration.
            $table->unsignedBigInteger('scan_id')->nullable();
            //$table->foreign('scan_id')->references('id')->on('scans');

            $table->string('slug');
            $table->string('short');
            $table->json('name');
            $table->integer('order');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('steps');
    }
};
