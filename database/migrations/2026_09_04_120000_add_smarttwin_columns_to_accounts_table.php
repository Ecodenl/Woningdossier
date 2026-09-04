<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The SmartTwin user id is what an incoming webhook identifies the account by, so it needs to be
     * a column we can index instead of a path inside the `extra` JSON. It is unique on their side:
     * one e-mail address is one SmartTwin user, and the e-mail address belongs to the account.
     */
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('smarttwin_user_id')->nullable()->unique()->after('email');
            $table->unsignedTinyInteger('smarttwin_user_role')->nullable()->after('smarttwin_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropUnique(['smarttwin_user_id']);
            $table->dropColumn(['smarttwin_user_id', 'smarttwin_user_role']);
        });
    }
};
