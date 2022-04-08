<?php

namespace App\Services;

use GuzzleHttp\Client;

class DiscordNotifier {

    public Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function notify(string $message)
    {
        $key = config('hoomdossier.webhooks.discord');

        if (! empty($key)) {
            $this->client->post($key, [
                'form_params' => [
                    'content' => $message
                ]]);
        }

    }
};