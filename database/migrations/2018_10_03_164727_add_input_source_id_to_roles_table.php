<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInputSourceIdToRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('roles', 'input_source_id')) {
            Schema::table('roles',
                function (Blueprint $table) {
                    $table->integer('input_source_id')->unsigned()->nullable()->after('guard_name');
                    $table->foreign('input_source_id')->references('id')->on('input_sources')->onDelete('set null');
                });
        }

        $coachInputSource = \App\Models\InputSource::where('short', 'coach')->first();
        DB::table('roles')->where('name', 'coach')->update(['input_source_id' => $coachInputSource->id]);

        $residentInputSource = \App\Models\InputSource::where('short', 'resident')->first();
        DB::table('roles')->where('name', 'resident')->update(['input_source_id' => $residentInputSource->id]);

        $cooperationInputSource = \App\Models\InputSource::where('short', 'cooperation')->first();
        DB::table('roles')->where('name', 'coordinator')->update(['input_source_id' => $cooperationInputSource->id]);
        DB::table('roles')->where('name', 'cooperation-admin')->update(['input_source_id' => $cooperationInputSource->id]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
