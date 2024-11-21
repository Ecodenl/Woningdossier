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
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');

            $table->string('email')->unique();
            $table->string('password');

            $table->text('two_factor_secret')->nullable();

            $table->text('two_factor_recovery_codes')->nullable();

            // We want this even if not enabled, we might need it in the future
            $table->timestamp('two_factor_confirmed_at')->nullable();

            $table->rememberToken();
            $table->timestamp('email_verified_at')->nullable();

            $table->string('old_email')->nullable();
            $table->string('old_email_token')->nullable();

            $table->boolean('active')->default(1);
            $table->boolean('is_admin')->default(false);

            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
        });
        Schema::dropIfExists('accounts');
    }
};
