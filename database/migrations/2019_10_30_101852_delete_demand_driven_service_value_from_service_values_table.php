<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $ventilationServiceValue = DB::table('services')
            ->where('short', 'house-ventilation')->first();

        if ($ventilationServiceValue instanceof \stdClass) {
            DB::table('service_values')
                ->where('service_id', $ventilationServiceValue->id)
                ->where('calculate_value', 5)
               ->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
