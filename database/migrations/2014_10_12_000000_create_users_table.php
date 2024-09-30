<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');

            $table->string('first_name')->default('');
            $table->string('last_name')->default('');

            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->string('confirm_token', 64)->nullable();

            $table->string('phone_number')->default('');

            $table->dateTime('tool_last_changed_at')->nullable()->default(null);

            if (!Schema::hasColumn('users', 'last_visited_url')) {
                $table->text('last_visited_url')->nullable()->default(null);
            }


            $table->boolean('active')->default(1);
            $table->boolean('is_admin')->default(false);
            $table->dateTime('regulations_refreshed_at')->nullable()->default(null);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
