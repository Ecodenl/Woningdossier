<?php

namespace App\Console\Commands\Upgrade;

use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Services\UserActionPlanAdviceService;
use Illuminate\Console\Command;

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
        $this->info('Mapping categories for user_action_plan_advices...');
        $this->mapUserActionPlanAdvices();
    }

    public function mapUserActionPlanAdvices()
    {
        // This will add the category to each row in the user_action_plan_advices table
        $userActionPlanAdvices = UserActionPlanAdvice::allInputSources()
            ->with('inputSource', 'step', 'user')
            ->get();

        $categoryMap = [
            "Ja, op korte termijn" => UserActionPlanAdviceService::CATEGORY_TO_DO,
            "Ja, op termijn" => UserActionPlanAdviceService::CATEGORY_LATER,
            "Misschien, meer informatie gewenst" => UserActionPlanAdviceService::CATEGORY_LATER,
            "Nee, geen interesse" => UserActionPlanAdviceService::CATEGORY_COMPLETE,
            "Nee, niet mogelijk / reeds uitgevoerd" => UserActionPlanAdviceService::CATEGORY_COMPLETE,
        ];

        foreach ($userActionPlanAdvices as $userActionPlanAdvice) {
            $user = $userActionPlanAdvice->user;

            $interest = $user->userInterestsForSpecificType(Step::class, $userActionPlanAdvice->step->id, $userActionPlanAdvice->inputSource)
                ->with('interest')->first()->interest;

            $userActionPlanAdvice->update([
                'category' => $categoryMap[$interest->name] ?? UserActionPlanAdviceService::CATEGORY_LATER,
            ]);
        }
    }
}
