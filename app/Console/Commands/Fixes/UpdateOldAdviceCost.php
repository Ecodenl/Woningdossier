<?php

namespace App\Console\Commands\Fixes;

use App\Services\UserActionPlanAdviceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateOldAdviceCost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixes:update-old-advice-costs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixes old user action plan advices that for some reason still have a numeric value';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        DB::table('user_action_plan_advices')
            ->whereRaw("JSON_EXTRACT(costs, '$.to') IS NULL")
            ->whereRaw("JSON_EXTRACT(costs, '$.from') IS NULL")
            ->whereNotNull('costs')
            ->orderBy('id')
            ->eachById(function ($advice) {
                $costs = $advice->costs;

                DB::table('user_action_plan_advices')
                    ->where('id', $advice->id)
                    ->update([
                        'costs' => json_encode(UserActionPlanAdviceService::formatCosts($costs)),
                    ]);
            });

        return 0;
    }
}
