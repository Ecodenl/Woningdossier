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
        //Schema::whenTableHasColumn('private_message_views', 'to_cooperation_id', function (Blueprint $table) {
        //    $table->dropForeign(['to_cooperation_id']);
        //    $table->dropColumn('to_cooperation_id');
        //});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
