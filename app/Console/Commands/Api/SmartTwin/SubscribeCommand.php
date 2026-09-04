<?php

namespace App\Console\Commands\Api\SmartTwin;

use App\Services\SmartTwin\Api\SmartTwinApi;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SubscribeCommand extends Command
{
    protected $signature = 'api:smarttwin:subscribe
                            {--name= : Subscriber name (defaults to app name)}';

    protected $description = 'Register an event subscription with the SmartTwin API. Outputs the subscriptionId to store in SMARTTWIN_SUBSCRIPTION_ID.';

    public function handle(SmartTwinApi $api): int
    {
        $subscriberName = $this->option('name') ?? config('app.name');
        $callbackUrl = route('api.v1.smarttwin.store');
        $signKey = config('hoomdossier.services.smarttwin.sign-key', '') ?: null;

        $this->info('Subscribing to SmartTwin events...');
        $this->line("  Subscriber : {$subscriberName}");
        $this->line("  Callback   : {$callbackUrl}");

        $response = $api->events()->subscribe($subscriberName, $callbackUrl, $signKey);

        $subscriptionId = $response['subscriptionId'] ?? $response['Value'] ?? null;

        if (! $subscriptionId) {
            $this->error('Subscription failed: no subscriptionId in response.');
            return self::FAILURE;
        }

        Log::info('SmartTwin event subscription created', [
            'subscriptionId' => $subscriptionId,
            'subscriberName' => $subscriberName,
            'callbackUrl'    => $callbackUrl,
        ]);

        $this->info('Subscribed successfully!');
        $this->newLine();
        $this->comment('Add the following to your .env file:');
        $this->line("SMARTTWIN_SUBSCRIPTION_ID={$subscriptionId}");

        return self::SUCCESS;
    }
}
