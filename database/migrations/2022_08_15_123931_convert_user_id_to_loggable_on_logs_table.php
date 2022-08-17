<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ConvertUserIdToLoggableOnLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('logs', 'user_id')) {
            $tableHasForeign = false;
            // TODO: If we need this more often, perhaps move to a helper or service
            foreach (Schema::getConnection()->getDoctrineSchemaManager()->listTableForeignKeys('logs') as $info) {
                if (in_array('user_id', $info->getColumns())) {
                    $tableHasForeign = true;
                    break;
                }
            }

            // Ensure we don't drop the foreign if it doesn't exist
            if ($tableHasForeign) {
                // Separate statements, because otherwise it won't drop the damn foreign before attempting the column change
                Schema::table('logs', function (Blueprint $table) {
                    $table->dropForeign(['user_id']);
                });
            }

            Schema::table('logs', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->change();
                $table->renameColumn('user_id', 'loggable_id');
                $table->string('loggable_type')->nullable()->after('id');
            });

            DB::table('logs')->update([
                'loggable_type' => User::class,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasColumn('logs', 'user_id')) {
            Schema::table('logs', function (Blueprint $table) {
                $table->dropColumn('loggable_type');
                $table->renameColumn('loggable_id', 'user_id');
            });
            Schema::table('logs', function (Blueprint $table) {
                $table->integer('user_id')->nullable()->default(null)->unsigned()->change();
                // As it turns out, MySQL is unable to foreign key a column with null values, so we will just
                // not set the foreign key...
                //$table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            });
        }
    }
}
