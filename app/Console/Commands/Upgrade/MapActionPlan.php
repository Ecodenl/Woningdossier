<?php

namespace App\Console\Commands\Upgrade;

use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\UserActionPlanAdvice;
use App\Models\UserEnergyHabit;
use App\Scopes\VisibleScope;
use App\Services\UserActionPlanAdviceService;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
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
        $this->info('Mapping renovation question to custom measure applications...');
        $this->mapRenovationToCustomMeasure();
    }

    public function mapUserActionPlanAdvices()
    {
        $ids = $this->argument('id');

        $query = $userActionPlanAdvices = UserActionPlanAdvice::allInputSources()
            ->whereNull('category');

        if (! empty($ids)) {
            $query->whereIn('user_id', $ids);
        }

        // This will add the category to each row in the user_action_plan_advices table
        $userActionPlanAdvices = $query->cursor();

        $bar = $this->output->createProgressBar($userActionPlanAdvices->count());
        $bar->start();

        foreach ($userActionPlanAdvices as $userActionPlanAdvice) {
            if ($userActionPlanAdvice->planned) {
                $category = UserActionPlanAdviceService::CATEGORY_TO_DO;
                $visible = true;
            } else {
                if (! empty($userActionPlanAdvice->planned_year)) {
                    if ($userActionPlanAdvice->planned_year <= now()->addYears(4)->format('Y')) {
                        $category = UserActionPlanAdviceService::CATEGORY_TO_DO;
                        $visible = true;
                    } else {
                        $category = UserActionPlanAdviceService::CATEGORY_LATER;
                        $visible = true;
                    }
                } else {
                    $category = UserActionPlanAdviceService::CATEGORY_LATER;
                    $visible = false;
                }
            }

            $userActionPlanAdvice->update([
                'category' => $category,
                'visible' => $visible,
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->output->newLine();
    }

    public function convertUserActionPlanAdvicesCostToJson()
    {
        $ids = $this->argument('id');

        $query = UserActionPlanAdvice::allInputSources();

        if (! empty($ids)) {
            $query->whereIn('user_id', $ids);
        }

        // This will convert the numeric cost to JSON
        $userActionPlanAdvices = $query->cursor();

        // We expect a DECIMAL column. This means we can't just set it to json
        // We do 2 things: we alter the table to TEXT so we can set the cost, and then
        // set it to JSON
        if ('json' !== Schema::getColumnType('user_action_plan_advices', 'costs')) {
            Schema::table('user_action_plan_advices', function (Blueprint $table) {
                $table->text('costs')->change();
            });
        }

        $bar = $this->output->createProgressBar($userActionPlanAdvices->count());
        $bar->start();

        // Loop each advice to alter the data
        foreach ($userActionPlanAdvices as $userActionPlanAdvice) {
            $costs = $userActionPlanAdvice->costs;

            if (! is_array($costs)) {
                if ($costs < 0) {
                    $newCosts = [
                        'from' => $costs,
                        'to' => null,
                    ];
                } else {
                    $newCosts = [
                        'from' => null,
                        'to' => $costs,
                    ];
                }

                $userActionPlanAdvice->update([
                    'costs' => $newCosts,
                ]);
            }

            $bar->advance();
        }

        // Convert column to json if not already JSON. We convert after, to prevent weird behaviour
        if ('json' !== Schema::getColumnType('user_action_plan_advices', 'costs')) {
            Schema::table('user_action_plan_advices', function (Blueprint $table) {
                $table->json('costs')->change();
            });
        }

        $bar->finish();
        $this->output->newLine();
    }

    public function mapRenovationToCustomMeasure()
    {
        $ids = $this->argument('id');

        $residentInputSource = InputSource::findByShort(InputSource::RESIDENT_SHORT);

        // Get all user energy habits that have renovation plans, we will map them to a custom measure
        // We don't need null (not answered) or 0 (no plans)
        $query = UserEnergyHabit::forInputSource($residentInputSource)
            ->whereNotNull('renovation_plans')
            ->where('renovation_plans', '!=', 0);

        if (! empty($ids)) {
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

            if (! $customMeasure instanceof CustomMeasureApplication) {
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


            UserActionPlanAdvice::withoutGlobalScope(VisibleScope::class)
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
