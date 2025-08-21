<?php

namespace App\Services;

use App\Traits\FluentCaller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;

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

    public function notify(string $message): void
    {
        // Max chars for Discord is 2000. We will split the message if needed.
        $messages = str_split($message, 1950);

        foreach ($messages as $message) {
            $this->sendMessage($message);
        }
    }

    private function sendMessage(string $message): void
    {
        // Logging locally always useful
        if (app()->isLocal()) {
            Log::debug($message);
        }

        if (! empty($this->key)) {
            try {
                $this->client->post($this->key, [
                    'form_params' => [
                        'content' => $message
                    ]
                ]);
            } catch (ClientException $e) {
                if ($e->getResponse()->getStatusCode() == 429) {
                    sleep(3);
                    $this->sendMessage($message);
                } else {
                    report($e);
                }
            }
        }
    }
}
