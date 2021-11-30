<?php

namespace App\Console\Commands\Upgrade;

use App\Models\Cooperation;
use Illuminate\Console\Command;

class MergeDatabases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:merge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merges the current live environments into the freshly migrated live database (deltawind.hoomdossier.nl env into hoomdossier.nl)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        // the cooperations which have sub live environments
        $mergeableCooperations = Cooperation::whereIn('slug', [
            'blauwvingerenergie',
            'cnme',
            'deltawind',
            'duec',
            'energiehuis',
            'leimuidenduurzaam',
            'lochemenergie',
            'nhec',
            'wijdemeren'
        ])->get();

        // first we will delete all the data of the cooperations on our migrated database.
        // After that we will merge the data from the corresponding  cooperation sub live database
        /** @var Cooperation $mergeableCooperation */
        foreach ($mergeableCooperations as $mergeableCooperation) {
            $mergeableCooperation->users()->delete();
        }

    }
}
