<?php

namespace App\Console\Commands\Upgrade;

use App\Models\Step;
use App\Scopes\NoGeneralDataScope;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MapComments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:map-comments {id?*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert general data comments to quick scan comments';

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
        $commentMap = [
            [
                'fromStep' => 'building-characteristics',
                'toStep' => 'building-data',
                'commentShort' => null,
            ],
            [
                'fromStep' => 'current-state',
                'toStep' => 'residential-status',
                'commentShort' => [
                    'service', 'element',
                ],
            ],
            [
                'fromStep' => 'usage',
                'toStep' => 'usage-quick-scan',
                'commentShort' => null,
            ],
            [
                'fromStep' => 'interest',
                'toStep' => 'living-requirements',
                'commentShort' => null,
            ],
        ];

        $ids = $this->argument('id');

        foreach ($commentMap as $commentMapData) {
            // Get relevant steps
            $fromStep = Step::withoutGlobalScope(NoGeneralDataScope::class)->where('short', $commentMapData['fromStep'])->first();
            $toStep = Step::withoutGlobalScope(NoGeneralDataScope::class)->where('short', $commentMapData['toStep'])->first();

            // If it's not an array, it's null. We will wrap it in an array to keep the code concise
            $shorts = is_array($commentMapData['commentShort']) ? $commentMapData['commentShort'] : [$commentMapData['commentShort']];
            foreach ($shorts as $short) {
                $baseQuery = DB::table('step_comments')
                    ->where('step_id', $fromStep->id)
                    ->where('short', $short);

                if (! empty($ids)) {
                    $baseQuery->whereIn('building_id', $ids);
                }

                $baseQuery->update([
                    'step_id' => $toStep->id,
                ]);
            }
        }
    }
}
