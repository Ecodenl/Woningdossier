<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A local copy of SmartTwin's solution catalogue, so the coupling screen has something to list.
     *
     * The couplings themselves live in `mappings`, keyed on `external_id`, not here: an advice can
     * name a solution this table has never seen, and that coupling still has to resolve.
     */
    public function up(): void
    {
        Schema::create('smarttwin_solutions', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Reads as `<kind of measure>|<provider>:<product>`, which is where `kind` comes from.
            $table->string('external_id');
            $table->string('name');
            $table->string('kind')->nullable();

            // When the catalogue last held this product. Products are never deleted here — an
            // existing advice may still refer to one — so this is what tells a withdrawn product
            // apart from a current one.
            $table->timestamp('last_seen_at')->nullable();

            $table->timestamps();

            $table->unique('external_id');
            $table->index('kind');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('smarttwin_solutions');
    }
};
