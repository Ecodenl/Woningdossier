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
        Schema::table('user_action_plan_advices', function (Blueprint $table) {
            $table->decimal('savings_gas', 65)->nullable()->change();
            $table->decimal('savings_electricity', 65)->nullable()->change();
            $table->decimal('savings_money', 65)->nullable()->change();
        });

        Schema::table('user_energy_habits', function (Blueprint $table) {
            $table->bigInteger('amount_electricity')->nullable()->change();
            $table->bigInteger('amount_gas')->nullable()->change();
            $table->bigInteger('amount_water')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // No need to roll back
    }
};
