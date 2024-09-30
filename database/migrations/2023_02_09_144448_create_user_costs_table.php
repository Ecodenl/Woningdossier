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
        Schema::create('user_costs', function (Blueprint $table) {
            $table->id();
            // Old tables use int, not bigint, so we can't use
            // $table->foreignId('input_source_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('input_source_id');
            $table->foreign('input_source_id')->references('id')->on('input_sources')->onDelete('cascade');
            $table->string('advisable_type');
            $table->unsignedBigInteger('advisable_id');
            $table->unsignedInteger('own_total')->nullable();
            $table->unsignedInteger('subsidy_total')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_costs');
    }
};
