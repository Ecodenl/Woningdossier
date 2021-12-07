<?php

namespace App\Console\Commands\Upgrade\Merge;

use App\Models\Cooperation;
use Illuminate\Console\Command;

class MergeAdjustedAutoIncrementTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'merge:user-building-tables {cooperation : The current cooperation database you want to merge eg; (deltawind into current)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merge the tables that have a building or user id.';

    const TABLES = [
        'accounts',
        'buildings',
        'users',
        'media',
        'private_messages',
        'question_options',
        'questionnaires',
        'questions',
        'example_buildings',
        'custom_measure_applications',
        'cooperation_measure_applications',
    ];
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
        foreach (self::TABLES as $table) {

        }
    }
}
