<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Fortify\Fortify;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // NOTE: We keep the name the same as to not publish it a potential second time (Fortify), but we don't
        // actually do anything.
        // This is because we have it on the accounts table, which was previously handled by the
        // migration '2019_06_26_111613_add_two_factor_columns_to_accounts_table', but now is just present
        // on the '2019_06_26_111612_create_accounts_table' migration.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
