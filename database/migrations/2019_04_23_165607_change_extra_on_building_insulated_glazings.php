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
        Schema::table('building_insulated_glazings', function (Blueprint $table) {
            $table->text('extra')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('building_insulated_glazings', function (Blueprint $table) {
            $table->string('extra')->nullable()->default(null)->change();
        });
    }
};
