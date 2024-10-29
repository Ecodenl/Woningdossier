<?php

use App\Models\MeasureApplication;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Measure application updates
        $updates = [
            'facade-wall-insulation' => [
                'costs' => 96,
            ],
            'roof-insulation-pitched-inside' => [
                'costs' => 96,
            ],
            'roof-insulation-flat-current' => [
                'costs' => 65,
            ],
            'high-efficiency-boiler-replace' => [
                'costs' => 2100,
            ],
            'heater-place-replace' => [
                'costs' => 3000,
            ],
        ];

        foreach ($updates as $short => $new) {
            MeasureApplication::where('short', '=', $short)
                              ->where(function ($q) {
                                  $q->whereNull('updated_at')
                                    ->orWhere('updated_at', '<',
                                        new Carbon('2019-10-14 00:00:00'));
                              })
                              ->update($new);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
