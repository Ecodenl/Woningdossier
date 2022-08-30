<?php

use App\Helpers\DataTypes\Caster;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDataTypeColumnToToolQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('tool_questions', 'data_type')) {
            Schema::table('tool_questions', function (Blueprint $table) {
                $table->string('data_type')->default(Caster::STRING)->after('placeholder');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('tool_questions', 'data_type')) {
            Schema::table('tool_questions', function (Blueprint $table) {
                $table->dropColumn('data_type');
            });
        }
    }
}
