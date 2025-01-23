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
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');

            // NOTE: Accounts table gets created later, so foreign is added on
            // the '2019_06_26_111612_create_accounts_table' migration.
            $table->integer('account_id')->unsigned()->nullable();
            //$table->foreign('account_id')->references('id')->on('accounts')->onDelete('restrict');

            // NOTE: Cooperations table gets created later, so foreign is added on
            // the '2014_11_01_041500_create_cooperations_table' migration.
            $table->unsignedInteger('cooperation_id')->nullable();
            //$table->foreign('cooperation_id')->references('id')->on('cooperations')->onDelete('cascade');

            $table->string('first_name')->default('');
            $table->string('last_name')->default('');

            $table->string('phone_number')->default('');
            $table->text('last_visited_url')->nullable();
            $table->json('extra')->nullable();
            $table->boolean('allow_access')->default(0);
            $table->dateTime('tool_last_changed_at')->nullable();
            $table->dateTime('regulations_refreshed_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
