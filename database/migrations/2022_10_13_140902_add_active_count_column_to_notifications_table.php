<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActiveCountColumnToNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (! Schema::hasColumn('notifications', 'active_count')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->unsignedInteger('active_count')->default(0)->after('is_active');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('notifications', 'active_count')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropColumn('active_count');
            });
        }
    }
}
