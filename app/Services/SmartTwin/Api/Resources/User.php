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

    /**
     * Log a user in and retrieve a short-lived JWT for them.
     *
     * Note: shares its URI with delete() — the HTTP verb (POST vs DELETE) disambiguates.
     */
    public function login(string $userId): array
    {
        return $this->client->post($this->uri($userId));
    }
}
