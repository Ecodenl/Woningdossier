<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * SmartTwin keys a user on their e-mail address and rejects a second account for one that already
     * exists. Our e-mail address lives on the account, while the link used to be stored per user
     * (so per cooperation), which meant every user beyond the first got a 400 and no link at all.
     * Move the link to where the identity actually is: one account, one SmartTwin user.
     *
     * Mirrors users.extra rather than adding dedicated columns, so the role we sent — and whatever
     * else SmartTwin makes us track — fits without another migration.
     */
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->json('extra')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('extra');
        });
    }
};
