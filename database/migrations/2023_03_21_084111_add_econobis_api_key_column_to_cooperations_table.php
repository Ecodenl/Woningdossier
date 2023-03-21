<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEconobisApiKeyColumnToCooperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('cooperations', 'econobis_api_key')) {
            Schema::table('cooperations', function (Blueprint $table) {
                $column = Schema::hasColumn('cooperations', 'econobis_wildcard') ? 'econobis_wildcard' : 'website_url';
                $table->longText('econobis_api_key')->nullable()->after($column);
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
        if (Schema::hasColumn('cooperations', 'econobis_api_key')) {
            Schema::table('cooperations', function (Blueprint $table) {
                $table->dropColumn('econobis_api_key');
            });
        }
    }
}
