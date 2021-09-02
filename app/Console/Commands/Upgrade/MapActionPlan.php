<?php

namespace App\Console\Commands\Upgrade;

use App\Models\UserActionPlanAdvice;
use App\Services\UserActionPlanAdviceService;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
        $this->info('Converting cost from int to JSON...');
        $this->convertUserActionPlanAdvicesCostToJson();
        $this->info('Mapping categories for user_action_plan_advices...');
        $this->mapUserActionPlanAdvices();
    }

    public function mapUserActionPlanAdvices()
    {
        // This will add the category to each row in the user_action_plan_advices table
        $userActionPlanAdvices = UserActionPlanAdvice::allInputSources()
                                                     ->whereNull('category')
                                                     ->cursor();

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
        // This will convert the numeric cost to JSON
        $userActionPlanAdvices = UserActionPlanAdvice::allInputSources()
            ->cursor();

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
                if ($costs < 0){
                    $newCosts = [
                        'from' => $costs,
                        'to' => null,
                    ];
                }
                else {
                    $newCosts = [
                        'from' => null,
                        'to'   => $costs,
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
}
