<?php

namespace App\Console\Commands;

use App\Helpers\NumberFormatter;
use App\Models\ExampleBuildingContent;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class FixExampleBuildings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:example-buildings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corrects whatever needs to be corrected.';

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
        foreach (ExampleBuildingContent::all() as $ebContent) {
            $content = $ebContent->content;
            $dot = Arr::dot($content);
            foreach ($dot as $key => $value) {
                if (Str::endsWith($key, ['surface', 'm2']) && ! is_null($value)) {
                    $newValue = NumberFormatter::mathableFormat($value, 2);
                    $this->info("Formatting: {$value} => {$newValue}");
                    Arr::set($content, $key, $newValue);
                }
            }
            $ebContent->content = $content;
            $ebContent->save();
        }
    }
}
