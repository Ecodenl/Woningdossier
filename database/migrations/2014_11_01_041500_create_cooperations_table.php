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
        // Necessary due to renaming! After deployment, remove wrapper again
        if (! Schema::hasTable('cooperations')) {
            Schema::create('cooperations', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('slug');
                $table->string('country')->default(\App\Enums\Country::NL->value);
                $table->string('cooperation_email')->nullable();
                $table->string('website_url')->nullable();
                $table->string('econobis_wildcard')->nullable();
                $table->longText('econobis_api_key')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });

            Schema::table('users', function (Blueprint $table) {
                $table->foreign('cooperation_id')->references('id')->on('cooperations')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['cooperation_id']);
        });
        Schema::dropIfExists('cooperations');
    }
};
