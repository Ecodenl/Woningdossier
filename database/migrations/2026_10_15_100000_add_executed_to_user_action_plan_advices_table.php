<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_action_plan_advices', function (Blueprint $table) {
            // A measure the resident carried out: when, and in their own words. Its documents hang
            // on the building, see App\Services\ExecutedAdviceService.
            $table->date('executed_at')->nullable()->after('extra');
            $table->text('executed_description')->nullable()->after('executed_at');
        });
    }

    public function down(): void
    {
        Schema::table('user_action_plan_advices', function (Blueprint $table) {
            $table->dropColumn(['executed_at', 'executed_description']);
        });
    }
};
