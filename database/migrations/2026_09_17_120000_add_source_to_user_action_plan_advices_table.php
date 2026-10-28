<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_action_plan_advices', function (Blueprint $table) {
            // Who owns this advice, see App\Enums\AdviceSource. Null means the calculation does,
            // which is every row that exists today and every row a dossier without an external
            // advice will ever hold.
            $table->string('source')->nullable()->after('step_id');
            $table->index('source');
        });
    }

    public function down(): void
    {
        Schema::table('user_action_plan_advices', function (Blueprint $table) {
            $table->dropIndex(['source']);
            $table->dropColumn('source');
        });
    }
};
