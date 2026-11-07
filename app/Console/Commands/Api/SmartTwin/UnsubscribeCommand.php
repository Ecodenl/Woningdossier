<?php

namespace App\Console\Commands\Api\SmartTwin;

use App\Services\SmartTwin\Api\SmartTwinApi;
use Illuminate\Console\Command;

class UnsubscribeCommand extends Command
{
    protected $signature = 'api:smarttwin:unsubscribe
                            {subscriptionId? : The subscription ID to cancel (defaults to SMARTTWIN_SUBSCRIPTION_ID)}';

    protected $description = 'Cancel an event subscription with the SmartTwin API.';

    public function handle(SmartTwinApi $api): int
    {
        $subscriptionId = $this->argument('subscriptionId')
            ?? config('hoomdossier.services.smarttwin.subscription-id');

        if (! $subscriptionId) {
            $this->error('No subscriptionId provided. Pass it as argument or set SMARTTWIN_SUBSCRIPTION_ID in .env.');
            return self::FAILURE;
        }

        $this->info("Unsubscribing from SmartTwin events (ID: {$subscriptionId})...");

        $api->events()->unsubscribe($subscriptionId);

        $this->info('Unsubscribed successfully.');

        return self::SUCCESS;
    }
}
