<?php

namespace App\Services;

use App\Traits\FluentCaller;
use GuzzleHttp\Client;

class DiscordNotifier
{
    use FluentCaller;

    use FluentCaller;

    public Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function notify(string $message)
    {
        $key = config('hoomdossier.webhooks.discord');

        if (! empty($key)) {
            try {
                $this->client->post($key, [
                    'form_params' => [
                        'content' => $message
                    ]
                ]);
            } catch (\Exception $e) {
                if (app()->bound('sentry')) {
                    app('sentry')->captureException($e);
                }
            }
        }

    }
};