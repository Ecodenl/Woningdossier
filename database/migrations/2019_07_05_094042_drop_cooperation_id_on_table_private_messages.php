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
        Schema::table('private_messages', function (Blueprint $table) {
            $table->dropForeign(['cooperation_id']);
            $table->dropColumn('cooperation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('private_messages', function (Blueprint $table) {
            $table->unsignedInteger('cooperation_id')->nullable()->default(null)->after('building_id');
            $table->foreign('cooperation_id')->references('id')->on('cooperations')->onDelete('cascade');
        });

        foreach (DB::table('private_messages')->get() as $privateMessage) {
            DB::table('private_messages')->where('id', $privateMessage->id)->update([
                'cooperation_id' => $privateMessage->to_cooperation_id,
            ]);
        }
    }
};
