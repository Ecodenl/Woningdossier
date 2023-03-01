<?php

namespace Tests;

use Ecodenl\LvbagPhpWrapper\Client;
use Mockery\MockInterface;

trait MocksLvbag
{
    private function mockLvbagClientWoonplaats(string $municipalityName)
    {
        $mockedApiData = [
            "_embedded" => [
                "bronhouders" => [
                    [
                        "naam" => $municipalityName,
                    ],
                ],
            ],
        ];
        $this->partialMock(
            Client::class,
            function (MockInterface $mock) use ($mockedApiData) {
                return $mock
                    ->shouldReceive('get')
                    ->andReturn($mockedApiData);
            }
        );
    }
}