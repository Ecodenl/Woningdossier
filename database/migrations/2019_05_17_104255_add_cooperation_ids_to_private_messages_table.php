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
        $privateMessages = DB::table('private_messages')->get();

        foreach ($privateMessages as $privateMessage) {
            DB::table('private_messages')
              ->where('id', $privateMessage->id)
              ->update(['cooperation_id' => $privateMessage->to_cooperation_id]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        DB::table('private_messages')->update(['cooperation_id' => null]);
    }
};
