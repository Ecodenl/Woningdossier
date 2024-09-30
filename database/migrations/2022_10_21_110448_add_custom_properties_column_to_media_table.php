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
        Schema::table('media', function (Blueprint $table) {
            $table->unsignedInteger('input_source_id')->nullable()->after('original_media_id');
            $table->foreign('input_source_id')->references('id')->on('input_sources')->onDelete('set null');

            $table->json('custom_properties')->nullable()->after('input_source_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropForeign(['input_source_id']);
            $table->dropColumn(['custom_properties', 'input_source_id']);
        });
    }
};
