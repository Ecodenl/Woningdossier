<?php

namespace App\Services\SmartTwin\Api;

use App\Services\SmartTwin\Api\Resources\Advice;
use App\Services\SmartTwin\Api\Resources\EventSubscription;
use App\Services\SmartTwin\Api\Resources\User;
use App\Traits\FluentCaller;

class SmartTwinApi
{
    use FluentCaller;

    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function user(): User
    {
        return new User($this->client, 'api/account/v1/users');
    }

    public function advice(): Advice
    {
        return new Advice($this->client, 'api/advice/v1');
    }

    public function events(): EventSubscription
    {
        return new EventSubscription($this->client, 'api/advice/v1/events');
    }
}
