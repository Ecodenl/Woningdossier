<?php

namespace App\Console\Commands\Upgrade\LiteScan;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DeleteCompletedStep extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:lite-scan:delete-completed-step';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete completed step due to new sub step.';

    public function handle()
    {
        $step = DB::table('steps')->whereShort('building-data')->first();
        DB::table('completed_steps')->where('step_id', $step->id)->delete();
    }
}