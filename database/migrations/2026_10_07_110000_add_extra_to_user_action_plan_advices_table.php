<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_action_plan_advices', function (Blueprint $table) {
            // What the owner of an advice knows about it beyond its price, namespaced per owner:
            // `smart_twin` holds App\Services\SmartTwin\SmartTwinAdviceDetails. Null for every
            // calculated advice.
            $table->json('extra')->nullable()->after('source');
        });
    }

    public function down(): void
    {
        Schema::table('user_action_plan_advices', function (Blueprint $table) {
            $table->dropColumn('extra');
        });
    }
};
