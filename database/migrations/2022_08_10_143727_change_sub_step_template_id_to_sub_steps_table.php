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
        Schema::table('sub_steps', function (Blueprint $table) {
            $table->unsignedBigInteger('sub_step_template_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // in order to revert this migration, we have to delete all the null values
        DB::table('sub_steps')->where('sub_step_template_id', null)->delete();

        Schema::table('sub_steps', function (Blueprint $table) {
            $table->unsignedBigInteger('sub_step_template_id')->nullable(false)->change();
        });
    }
};
