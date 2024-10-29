<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Only applies to accept and local currently. So no rollback is required.
     */

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('building_permissions', 'role_id')) {
            Schema::table('building_permissions',
                function (Blueprint $table) {
                    $table->dropForeign('building_permissions_role_id_foreign');
                    $table->dropColumn('role_id');
                });
        }

        if (Schema::hasColumn('building_permissions', 'step_id')) {
            Schema::table('building_permissions',
                function (Blueprint $table) {
                    $table->dropForeign('building_permissions_step_id_foreign');
                    $table->dropColumn('step_id');
                });
        }

        if (Schema::hasColumn('building_permissions', 'permissions')) {
            Schema::table('building_permissions',
                function (Blueprint $table) {
                    $table->dropColumn('permissions');
                });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('building_permissions',
            function (Blueprint $table) {
                $table->integer('role_id')->unsigned()->nullable();
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');

                $table->integer('step_id')->unsigned()->nullable();
                $table->foreign('step_id')->references('id')->on('steps')->onDelete('set null');

                $table->text('permissions');
            });
    }
};
