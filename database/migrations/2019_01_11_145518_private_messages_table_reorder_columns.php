<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PrivateMessagesTableReorderColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('private_messages', function (Blueprint $table) {

            // drop columns
            $table->dropColumn(['title', 'main_message', 'status', 'is_completed', 'from_user_read', 'to_user_read']);

            // add the new columns
            $table->integer('building_id')->unsigned()->nullable()->default(null)->after('id');
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('set null');

            $table->string('from_user')->default("")->after('building_id');

            $table->boolean('is_public')->nullable()->after('building_id');

        });

        Schema::table('private_messages', function (Blueprint $table) {

            // change le data
            $pm = \DB::table('private_messages')->get();
            foreach ($pm as $privateMessage) {

                $user = \DB::table('users')->find($privateMessage->from_user_id);
                $building = \DB::table('buildings')->where('user_id', $user->id)->first();

                $fromUser = "{$user->first_name} {$user->last_name}";

                \DB::table('private_messages')
                    ->where('id', $privateMessage->id)
                    ->update([
                        'from_user' => $fromUser,
                        'building_id' => $building->id
                    ]);

            }

            $table->dropForeign(['to_user_id']);
            $table->dropColumn(['to_user_id']);

        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('private_messages', function (Blueprint $table) {

            $table->dropColumn('is_public');
            $table->string('title')->nullable()->default(null)->after('building_id');

            $table->integer('to_user_id')->unsigned()->nullable()->default(null)->after('from_user_id');
            $table->foreign('to_user_id')->references('id')->on('users')->onDelete('cascade');

            $table->boolean('from_user_read')->default(false)->after('to_user_id');
            $table->boolean('to_user_read')->default(false)->after('from_user_read');

            $table->integer('main_message')->nullable()->default(null)->after('title');

            $table->string('status')->nullable()->after('request_type');
            $table->boolean('is_completed')->default(false)->after('status');

            $table->dropForeign(['building_id']);
            $table->dropColumn(['building_id', 'from_user']);

        });


    }
}
