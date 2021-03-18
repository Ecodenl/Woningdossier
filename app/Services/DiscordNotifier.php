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
        $this->client->post(config('hoomdossier.webhooks.discord'), [
            'form_params' => [
                'content' => $message
            ]]);
    }
};