<?php

namespace App\Console\Commands\Upgrade;

use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Models\UserEnergyHabit;
use App\Services\UserActionPlanAdviceService;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MapActionPlan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:map-action-plan {id?*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will map the action plan data to the new format';

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
        $this->info('Converting cost from int to JSON...');
        $this->convertUserActionPlanAdvicesCostToJson();
        $this->info('Mapping categories for user_action_plan_advices...');
        $this->mapUserActionPlanAdvices();
        $this->info('Mapping advices to master...');
        $this->mapAdviceMasterInputSource();
        $this->info('Mapping renovation question to custom measure applications...');
        $this->mapRenovationToCustomMeasure();
    }

    public function mapUserActionPlanAdvices()
    {
        // When the planned the measure but has no planned year we put it into the todo column.
        DB::table('user_action_plan_advices')
            ->where('planned', 1)
            ->whereNull('planned_year')
            ->update([
                'category' => UserActionPlanAdviceService::CATEGORY_TO_DO,
                'visible' => true
            ]);


        $year = now()->addYears(4)->format('Y');
        // for the user who did check the planned box but filled in he had this measure planned within the next 5 years
        DB::table('user_action_plan_advices')
            ->where('planned', 1)
            ->where('planned_year', "<=", $year)
            ->update([
                'category' => UserActionPlanAdviceService::CATEGORY_TO_DO,
                'visible' => true
            ]);

        // this does what the above does, but updates each advice above 2025.
        DB::table('user_action_plan_advices')
            ->where('planned', 1)
            ->where('planned_year', ">", $year)
            ->update([
                'category' => UserActionPlanAdviceService::CATEGORY_LATER,
                'visible' => true
            ]);

        // for the user who did not check the planned checkbox but filled in he had this measure planned within the next 5 years
        // so at time or writing that would be users where planned = false and planned year is equal or below 2025
        DB::table('user_action_plan_advices')
            ->where('planned', 0)
            ->where('planned_year', "<=", $year)
            ->update([
                'category' => UserActionPlanAdviceService::CATEGORY_TO_DO,
                'visible' => true
            ]);

        // this does what the above does, but updates each advice above 2025.
        DB::table('user_action_plan_advices')
            ->where('planned', 0)
            ->where('planned_year', ">", $year)
            ->update([
                'category' => UserActionPlanAdviceService::CATEGORY_LATER,
                'visible' => true
            ]);

        // handles the user who has absolutely 0 interest
        DB::table('user_action_plan_advices')
            ->where('planned', 0)
            ->where('planned_year', null)
            ->update([
                'category' => UserActionPlanAdviceService::CATEGORY_TO_DO,
                'visible' => false
            ]);
    }

    public function convertUserActionPlanAdvicesCostToJson()
    {
        // We expect a DECIMAL column. This means we can't just set it to JSON.
        // We do 2 things: we alter the table to TEXT so we can set the cost, and then
        // set it to JSON.
        if ('json' !== Schema::getColumnType('user_action_plan_advices', 'costs')) {
            Schema::table('user_action_plan_advices', function (Blueprint $table) {
                $table->text('costs')->change();
            });
        }

        // Update data quickly
        DB::statement("UPDATE user_action_plan_advices as a 
                JOIN user_action_plan_advices as b on a.id = b.id
                SET a.costs = CONCAT('{\"from\": ', b.costs, ', \"to\": null}')
                WHERE a.costs <= 0 AND a.costs NOT LIKE '%{%';"
        );

        DB::statement("UPDATE user_action_plan_advices
                SET costs = '{\"from\": null, \"to\": null}'
                WHERE costs IS NULL;"
        );

        DB::statement("UPDATE user_action_plan_advices as a 
                JOIN user_action_plan_advices as b on a.id = b.id
                SET a.costs = CONCAT('{\"from\": null', ', \"to\": ', b.costs, '}')
                WHERE a.costs > 0 AND a.costs NOT LIKE '%{%';"
        );

        // Convert column to JSON if not already JSON. We convert after, to prevent weird behaviour.
        if ('json' !== Schema::getColumnType('user_action_plan_advices', 'costs')) {
            Schema::table('user_action_plan_advices', function (Blueprint $table) {
                $table->json('costs')->change();
            });
        }
    }

    public function mapAdviceMasterInputSource()
    {
        $ids = $this->argument('id');

        if (! empty($ids)) {
            $users = DB::table('users')->whereIn('id', $ids)->cursor();
        } else {
            $users = DB::table('users')->cursor();
        }

        $masterInputSource = DB::table('input_sources')
            ->where('short', InputSource::MASTER_SHORT)
            ->first();
        $coachInputSource = DB::table('input_sources')
            ->where('short', InputSource::COACH_SHORT)
            ->first();
        $residentInputSource = DB::table('input_sources')
            ->where('short', InputSource::RESIDENT_SHORT)
            ->first();

        foreach ($users as $user) {
            $totalMasterAdvices = DB::table('user_action_plan_advices')
                ->where('user_id', $user->id)
                ->where('input_source_id', $masterInputSource->id)
                ->count();

            if ($totalMasterAdvices === 0) {
                $rowsToReplicate = DB::table('user_action_plan_advices')
                    ->where('user_id', $user->id)
                    ->where('input_source_id', $coachInputSource->id)
                    ->get();

                if ($rowsToReplicate->count() === 0) {
                    $rowsToReplicate = DB::table('user_action_plan_advices')
                        ->where('user_id', $user->id)
                        ->where('input_source_id', $residentInputSource->id)
                        ->get();
                }

                foreach ($rowsToReplicate as $row) {
                    $row->input_source_id = $masterInputSource->id;
                    $row = (array) $row;
                    unset($row['id']);
                    DB::table('user_action_plan_advices')->insert($row);
                }
            }
        }
    }

    public function mapRenovationToCustomMeasure()
    {
        $ids = $this->argument('id');

        $residentInputSource = InputSource::findByShort(InputSource::RESIDENT_SHORT);

        // Get all user energy habits that have renovation plans, we will map them to a custom measure
        // We don't need null (not answered) or 0 (no plans).
        $query = UserEnergyHabit::forInputSource($residentInputSource)
            ->whereNotNull('renovation_plans')
            ->where('renovation_plans', '!=', 0);

        if (!empty($ids)) {
            $query->whereIn('user_id', $ids);
        }

        $userEnergyHabits = $query->cursor();

        $bar = $this->output->createProgressBar($userEnergyHabits->count());
        $bar->start();

        foreach ($userEnergyHabits as $userEnergyHabit) {
            $user = $userEnergyHabit->user;
            $building = $user->building;

            $name = 'Renovatie';
            $info = 'Uw verbouwingsplannen';

            $customMeasure = CustomMeasureApplication::where('name->nl', $name)
                ->where('info->nl', $info)
                ->forBuilding($building)
                ->forInputSource($residentInputSource)
                ->first();

            if (!$customMeasure instanceof CustomMeasureApplication) {
                $customMeasure = CustomMeasureApplication::create(
                    [
                        'name' => ['nl' => $name],
                        'info' => ['nl' => $info],
                        'building_id' => $building->id,
                        'input_source_id' => $residentInputSource->id,
                        'hash' => Str::uuid(),
                    ]
                );
            }

            // 1 is within 2 years, 2 within 5 years
            $category = $userEnergyHabit->renovation_plans === 1 ? UserActionPlanAdviceService::CATEGORY_TO_DO
                : UserActionPlanAdviceService::CATEGORY_LATER;

            // We need the model, so this will trigger model events to automatically save it on the master input source.
            UserActionPlanAdvice::withInvisible()
                ->allInputSources()
                ->updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'user_action_plan_advisable_type' => CustomMeasureApplication::class,
                        'user_action_plan_advisable_id' => $customMeasure->id,
                        'input_source_id' => $residentInputSource->id,
                    ],
                    [
                        'visible' => true,
                        'category' => $category,
                    ],
                );

            $bar->advance();
        }

        $bar->finish();
        $this->output->newLine();
    }
}
