<?php

namespace App\Services;

use App\Traits\FluentCaller;
use GuzzleHttp\Client;

class DiscordNotifier
{
    use FluentCaller;

    public Client $client;

    private string $key = '';

    public function __construct()
    {
        $this->client = new Client();
        $this->key = config('hoomdossier.webhooks.discord', '') ?? '';
    }

    public function notify(string $message)
    {
        // Max chars for Discord is 2000. We will split the message if needed.
        $messages = str_split($message, 1950);

        foreach ($messages as $message) {
            $this->sendMessage($message);
        }
    }

    private function sendMessage(string $message)
    {
        if (! empty($this->key)) {
            try {
                $this->client->post($this->key, [
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