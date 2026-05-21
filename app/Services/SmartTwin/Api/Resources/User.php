<?php

namespace App\Services\SmartTwin\Api\Resources;

use App\Services\SmartTwin\Api\UserRole;
use GuzzleHttp\RequestOptions;

class User extends Resource
{
    public function create(string $email, string $firstName, string $lastName, UserRole $role): array
    {
        return $this->client->post($this->uri(), [
            RequestOptions::JSON => [
                'email'     => $email,
                'firstName' => $firstName,
                'lastName'  => $lastName,
                'role'      => $role->value,
            ],
        ]);
    }

    public function delete(string $userId): void
    {
        $this->client->delete($this->uri($userId));
    }
}
