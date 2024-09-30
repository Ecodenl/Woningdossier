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
        Schema::table('private_messages', function (Blueprint $table) {
            $table->dropColumn('request_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('private_messages', function (Blueprint $table) {
            $table->string('request_type')->after('from_user')->nullable();
        });
    }
};
