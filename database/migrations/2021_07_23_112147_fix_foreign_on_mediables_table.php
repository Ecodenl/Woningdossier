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
        // Needed to fix media_id foreign on mediables table; we do this because the vendor file uses `cascadeOnDelete()`,
        // which doesn't actually work and causes foreign key exceptions when deleting media

        Schema::disableForeignKeyConstraints();

        if (Schema::hasTable('mediables')) {
            Schema::table('mediables', function (Blueprint $table) {
                $table->dropForeign('mediables_media_id_foreign');
                $table->foreign('media_id')
                    ->references('id')->on('media')->onDelete('cascade');
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse
    }
};
