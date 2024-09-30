<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $serviceIdsToDelete = \DB::table('services')
            ->whereIn('short', ['hybrid-heat-pump', 'full-heat-pump'])
            ->select('id')
            ->get()->pluck('id')
            ->toArray();

        // delete the service values
        DB::table('service_values')->whereIn(
            'service_id', $serviceIdsToDelete
        )->delete();

        // and delete the services itself
        DB::table('services')->whereIn('short', ['hybrid-heat-pump', 'full-heat-pump'])->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
    }
};
