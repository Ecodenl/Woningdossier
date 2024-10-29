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
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
