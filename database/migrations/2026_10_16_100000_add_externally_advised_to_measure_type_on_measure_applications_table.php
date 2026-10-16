<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('measure_applications', function (Blueprint $table) {
            // Measures only an outside party advises: SmartTwin, for now. Hoomdossier asks nothing
            // about them and calculates nothing for them. A type of their own keeps them out of
            // everything that takes all energy saving measures, of a step or at all: the user cost
            // and execute questions, and the old expert pages, which break on a measure they do not
            // know. See MeasureApplication::EXTERNALLY_ADVISED.
            $table->enum('measure_type', ['energy_saving', 'maintenance', 'externally_advised'])->change();
        });
    }

    public function down(): void
    {
        Schema::table('measure_applications', function (Blueprint $table) {
            // Fails while externally advised measures exist, rather than deleting them along with
            // their couplings and the advices that point at them.
            $table->enum('measure_type', ['energy_saving', 'maintenance'])->change();
        });
    }
};
