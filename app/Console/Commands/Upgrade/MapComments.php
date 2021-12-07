<?php

namespace App\Console\Commands\Upgrade;

use App\Models\InputSource;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use stdClass;

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
            $fromStep = DB::table('steps')->where('short', $commentMapData['fromStep'])->first();
            $toStep = DB::table('steps')->where('short', $commentMapData['toStep'])->first();

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

        // We have mapped the comments. Now we need to add building complaints to the step comment of building data
        $step = DB::table('steps')->where('short', 'building-data')->first();
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        // Get all user energy habits that have building complaints, with the building attached
        $userEnergyHabits = DB::table('user_energy_habits')
            ->whereNotNull('building_complaints')
            ->where('input_source_id', '!=', $masterInputSource->id)
            ->select('user_energy_habits.*', 'buildings.id as building_id')
            ->leftJoin('buildings', 'buildings.user_id', '=', 'user_energy_habits.user_id')
            ->get();

        foreach ($userEnergyHabits as $userEnergyHabit) {
            $wheres = [
                ['building_id', $userEnergyHabit->building_id],
                ['input_source_id', $userEnergyHabit->input_source_id],
                ['step_id', $step->id],
            ];

            $comment = DB::table('step_comments')
                ->where($wheres)
                ->first();

            // If it exists, we append the complaints to the existing comment
            if ($comment instanceof stdClass) {
                DB::table('step_comments')
                    ->where('id', $comment->id)
                    ->update([
                        'comment' => $comment->comment . PHP_EOL . PHP_EOL . $userEnergyHabit->building_complaints,
                    ]);
            } else {
                // Else we obviously build a whole new comment
                DB::table('step_comments')->insert([
                    'building_id' => $userEnergyHabit->building_id,
                    'input_source_id' => $userEnergyHabit->input_source_id,
                    'step_id' => $step->id,
                    'comment' => $userEnergyHabit->building_complaints,
                ]);
            }
        }
    }
}
