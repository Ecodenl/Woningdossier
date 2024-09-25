<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('cooperations', 'econobis_wildcard')) {
            Schema::table('cooperations', function (Blueprint $table) {
                $table->string('econobis_wildcard')->after('website_url')->nullable();
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
        if (Schema::hasColumn('cooperations', 'econobis_wildcard')) {
            Schema::table('cooperations', function (Blueprint $table) {
                $table->dropColumn('econobis_wildcard');
            });
        }
    }
};
