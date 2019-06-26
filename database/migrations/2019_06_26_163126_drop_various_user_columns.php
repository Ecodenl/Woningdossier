<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropVariousUserColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn('first_name');
            $table->dropColumn('last_name');

            $table->dropColumn('email')->unique();
            $table->dropColumn('password');

            $table->dropColumn('remember_token');
            $table->dropColumn('confirm_token');

            $table->dropColumn('old_email');
            $table->dropColumn('old_email_token');

            $table->dropColumn('phone_number');
            $table->dropColumn('mobile');

            $table->dropColumn('last_visit');
            $table->dropColumn('visit_count');

            $table->dropColumn('active');
            $table->dropColumn('is_admin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->default('');
            $table->string('last_name')->default('');

            $table->string('email');
            $table->string('password');

            $table->rememberToken();
            $table->string('confirm_token', 64)->nullable();

            $table->string('old_email')->nullable()->default(null);
            $table->string('old_email_token')->nullable()->default(null);

            $table->string('phone_number')->default('');
            $table->string('mobile')->default('');

            $table->boolean('active')->default(1);
            $table->boolean('is_admin')->default(false);

        });
    }
}
