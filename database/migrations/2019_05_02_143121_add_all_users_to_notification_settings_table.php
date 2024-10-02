<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $notificationTypes = DB::table('notification_types')->get();
        $users = DB::table('users')->get();
        $interval = DB::table('notification_intervals')->where('short', 'no-interest')->first();

        foreach ($users as $user) {
            foreach ($notificationTypes as $notificationType) {
                DB::table('notification_settings')->insert([
                    'user_id'     => $user->id,
                    'type_id'     => $notificationType->id,
                    'interval_id' => $interval->id,
                    'last_notified_at' => null,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
