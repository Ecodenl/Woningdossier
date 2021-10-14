<?php

namespace App\Console\Commands\LegacyCleanup;

use App\Models\ExampleBuildingContent;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class DeleteExampleBuildingContentKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'legacy-cleanup:example-building-content';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to remove old keys from the existing example building content';

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

        $keysToRemove = [
            'heater.-.user_energy_habits',
            'solar-panels.-.building_pv_panels.total_installed_power',
        ];

        foreach(ExampleBuildingContent::all()->where('id', ) as $exampleBuildingContent) {
            $content = $exampleBuildingContent->content;
            // forget the keys we need to remove
            foreach($keysToRemove as $keyToRemove) {
                Arr::forget($content, $keyToRemove);
            }

            $exampleBuildingContent->update(compact('content'));
        }
    }
}
