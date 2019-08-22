<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');

            $table->string('email')->unique();
            $table->string('password');

            $table->rememberToken();
            $table->string('confirm_token', 64)->nullable();

            $table->string('old_email')->nullable()->default(null);
            $table->string('old_email_token')->nullable()->default(null);

            $table->boolean('active')->default(1);
            $table->boolean('is_admin')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}
