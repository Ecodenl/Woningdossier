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
        Schema::table('buildings', function (Blueprint $table) {
            $table->string('status')->after('user_id')->default('active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (Schema::hasColumn('buildings', 'status')) {
            Schema::table('buildings', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
