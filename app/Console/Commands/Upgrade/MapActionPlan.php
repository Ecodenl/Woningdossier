<?php

namespace App\Console\Commands\Upgrade;

use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\Translation;
use App\Models\UserActionPlanAdvice;
use App\Services\UserActionPlanAdviceService;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
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
    protected $signature = 'upgrade:map-action-plan';

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
//        $this->info('Mapping categories for user_action_plan_advices...');
//        $this->mapUserActionPlanAdvices();
        $this->info('Converting cost from int to JSON...');
        $this->convertUserActionPlanAdvicesCostToJson();
    }

    public function mapUserActionPlanAdvices()
    {
        // This will add the category to each row in the user_action_plan_advices table
        $userActionPlanAdvices = UserActionPlanAdvice::allInputSources()
            ->with('inputSource', 'step', 'user')
            ->get();

        $shortTerm = "Ja, op korte termijn";
        $term = "Ja, op termijn";

        $categoryMap = [
            $shortTerm => UserActionPlanAdviceService::CATEGORY_TO_DO,
            $term => UserActionPlanAdviceService::CATEGORY_LATER,
            "Misschien, meer informatie gewenst" => UserActionPlanAdviceService::CATEGORY_LATER,
            "Nee, geen interesse" => UserActionPlanAdviceService::CATEGORY_COMPLETE,
            "Nee, niet mogelijk / reeds uitgevoerd" => UserActionPlanAdviceService::CATEGORY_COMPLETE,
        ];

        foreach ($userActionPlanAdvices as $userActionPlanAdvice) {
            $user = $userActionPlanAdvice->user;

            $interest = $user->userInterestsForSpecificType(Step::class, $userActionPlanAdvice->step->id, $userActionPlanAdvice->inputSource)
                ->with('interest')->first()->interest;

            $name = $interest->name;
            // Get category on name base
            $category = $categoryMap[$name] ?? UserActionPlanAdviceService::CATEGORY_LATER;

            $year = $userActionPlanAdvice->year;
            // If it's a term/short term interest, we need to check the year
            if (($name === $shortTerm || $name === $term) && ! empty($year)) {
                $now = CarbonImmutable::now();

                // TODO: Check this, maybe they will be interchangeable and we only need the first if
                switch($name) {
                    case $shortTerm:
                        if ($year <= $now->addYears(4)->format('Y')) {
                            $category = UserActionPlanAdviceService::CATEGORY_TO_DO;
                        } else {
                            // TODO: Check this guessed map
                            $category = UserActionPlanAdviceService::CATEGORY_LATER;
                        }
                        break;

                    case $term:
                        if ($year >= $now->addYears(5)->format('Y')) {
                            $category = UserActionPlanAdviceService::CATEGORY_LATER;
                        } else {
                            // TODO: Check this guessed map
                            $category = UserActionPlanAdviceService::CATEGORY_TO_DO;
                        }
                        break;
                }
            }

            $userActionPlanAdvice->update([
                'category' => $category,
            ]);
        }
    }

    public function convertUserActionPlanAdvicesCostToJson()
    {
        // This will convert the numeric cost to JSON
        $userActionPlanAdvices = UserActionPlanAdvice::allInputSources()
            ->get();

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
                $newCosts = [
                    'from' => $costs,
                    'to' => null,
                ];

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
}
