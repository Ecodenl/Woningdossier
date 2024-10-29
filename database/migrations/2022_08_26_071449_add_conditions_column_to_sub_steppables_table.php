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
        if (! Schema::hasColumn('sub_steppables', 'conditions')) {
            Schema::table('sub_steppables', function (Blueprint $table) {
                $table->json('conditions')->after('tool_question_type_id')->nullable()->default(null);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('sub_steppables', 'conditions')) {
            Schema::table('sub_steppables', function (Blueprint $table) {
                $table->dropColumn('conditions');
            });
        }
    }
};
