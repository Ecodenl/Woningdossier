<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckForDuplicates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:duplicates ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for duplicates in various tables.';

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
        $client = new Client();

        $buildingServiceDuplicates = $this->buildingServicesDuplicate();
        $buildingElementsExceptWoodElementsDuplicates = $this->buildingElementsExceptWoodElementsDuplicate();
        $userInterestsDuplicates = $this->userInterestsDuplicate();
        $userActionPlanAdvicesDuplicates = $this->userActionPlanAdvicesDuplicate();
//
//        $this->table(
//            [
//                'building_service', 'building_elements', 'user_interests',' user_action_plan_advices',
//            ],
//            [
//                [$buildingServiceDuplicates->count(), $buildingElementsExceptWoodElementsDuplicates->count(), $userInterestsDuplicates->count(), $userActionPlanAdvicesDuplicates->count()]
//            ]
//        );

        if ($buildingServiceDuplicates->isNotEmpty()) {
            $this->notifyDiscord($client, "**{$buildingServiceDuplicates->count()} duplicates found in building_services** \n reproducible data:");
            foreach ($buildingServiceDuplicates as $buildingServiceDuplicate) {
                $content = DB::table('building_services')
                    ->where('building_id', $buildingServiceDuplicate->building_id)
                    ->get()
                    ->toArray();

                $this->sendDebuggableDataToDiscord($client, $content);
            }
        }

        if ($buildingElementsExceptWoodElementsDuplicates->isNotEmpty()) {
            $this->notifyDiscord($client, "**{$buildingElementsExceptWoodElementsDuplicates->count()} duplicates found in building_elements** \n reproducible data:");
            foreach ($buildingElementsExceptWoodElementsDuplicates as $buildingElementsExceptWoodElementsDuplicate) {
                $content = DB::table('building_elements')
                    ->where('building_id', $buildingElementsExceptWoodElementsDuplicate->building_id)
                    ->get()
                    ->toArray();

                $this->sendDebuggableDataToDiscord($client, $content);
            }
        }

        if ($userInterestsDuplicates->isNotEmpty()) {
            $this->notifyDiscord($client, "**{$userInterestsDuplicates->count()} duplicates found in user_interests** \n reproducible data:");
            foreach ($userInterestsDuplicates as $userInterestsDuplicate) {
                $content = DB::table('user_interests')
                    ->where('user_id', $userInterestsDuplicate->user_id)
                    ->get()->toArray();

                $this->sendDebuggableDataToDiscord($client, $content);
            }
        }

        if ($userActionPlanAdvicesDuplicates->isNotEmpty()) {
            $this->notifyDiscord($client, "**{$userActionPlanAdvicesDuplicates->count()} duplicates found in user_action_plan_advices** \n reproducible data:");
            foreach ($userActionPlanAdvicesDuplicates as $userActionPlanAdvicesDuplicate) {
                $content = DB::table('user_action_plan_advices')
                    ->where('user_id', $userActionPlanAdvicesDuplicate->user_id)
                    ->get()->toArray();

                $this->sendDebuggableDataToDiscord($client, $content);
            }
        }

        if (array_sum([$userInterestsDuplicates->count(), $userActionPlanAdvicesDuplicates->count(), $buildingElementsExceptWoodElementsDuplicates->count(), $buildingServiceDuplicates->count()]) === 0) {
            $this->notifyDiscord($client, "No duplicates have been found today.");
        }

    }

    private function sendDebuggableDataToDiscord($client, array $content)
    {
        // method to work around the max message length of discord
        $start = 0;
        $content = json_encode($content);
        $len = strlen($content);
        $maxDiscordMessageLength = 1950;

        while ($len > $maxDiscordMessageLength) {
            $str = substr($content, $start, $maxDiscordMessageLength);

            $len = $len - $maxDiscordMessageLength;
            $start = $start + $maxDiscordMessageLength;

            $this->notifyDiscord($client, "```$str```");
        }

        if($len < $maxDiscordMessageLength) {
            $content = substr($content, $start, $maxDiscordMessageLength);
            $this->notifyDiscord($client, "```$content```");
        }
    }

    private function notifyDiscord(Client $client, $message)
    {
//        if (config('app.env') === 'production') {
            sleep(1);
            $client->post(config('hoomdossier.webhooks.discord'), [
                'form_params' => [
                    'content' => $message
                ]]);
//        }
    }


    private function userInterestsDuplicate()
    {
        return DB::table('user_interests')
            ->selectRaw('user_id, input_source_id, interested_in_type, interested_in_id, count(*)')
            ->groupBy([
                'user_id',
                'input_source_id',
                'interested_in_type',
                'interested_in_id',
            ])
            ->having('count(*)', '>', 1)
            ->get();

    }

    private function userActionPlanAdvicesDuplicate()
    {
        return DB::table('user_action_plan_advices')
            ->selectRaw('user_id, input_source_id, measure_application_id, step_id, count(*)')
            ->groupBy([
                'user_id',
                'input_source_id',
                'measure_application_id',
                'step_id',
            ])
            ->having('count(*)', '>', 1)
            ->get();

    }

    private function buildingServicesDuplicate()
    {
        return DB::table('building_services')
            ->selectRaw('building_id, input_source_id, service_id, count(*)')
            ->groupBy([
                'building_id',
                'input_source_id',
                'service_id',
            ])
            ->having('count(*)', '>', 1)
            ->get();
    }

    /** Wood elements are a different cup of tea, and currently has 0 duplicates */
    private function buildingElementsExceptWoodElementsDuplicate()
    {
        // Get all the duplicate building elements, grouped on inputsource, elementid en building id
        return DB::table('building_elements')
            ->selectRaw('building_id, input_source_id, element_id, count(*)')
            ->where('element_id', '!=', 8)
            ->groupBy([
                'building_id',
                'input_source_id',
                'element_id',
            ])
            ->having('count(*)', '>', 1)
            ->get();
    }
}
