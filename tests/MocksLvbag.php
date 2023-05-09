<?php

namespace Tests;

use Ecodenl\LvbagPhpWrapper\Client;
use Mockery\MockInterface;

trait MocksLvbag
{
    private array $mockedApiData = [];

    private function mockLvbagClientWoonplaats(string $municipalityName): self
    {
        $this->mockedApiData['_embedded']['bronhouders'] = [
            [
                "naam" => $municipalityName,
            ],
        ];
        return $this;
    }

    private function mockLvbagClientAdresUitgebreid(array $fallbackData): self
    {
        $this->mockedApiData['_embedded']['adressen'] = [
            [
                "nummeraanduidingIdentificatie" => "1924200000030235",
                "woonplaatsIdentificatie" => "2134",
                "openbareRuimteNaam" => "Boezemweg",
                "huisnummer" => $fallbackData['number'],
                "postcode" => $fallbackData['postal_code'],
                'huisletter' => null,
                'huisnummertoevoeging' => $fallbackData['extension'],
                "woonplaatsNaam" => "Oude-Tonge",
                "oorspronkelijkBouwjaar" => [
                    0 => "2015"
                ],
                "oppervlakte" => 2666,
            ],
        ];
        return $this;
    }

    private function createLvbagMock()
    {
        $this->partialMock(
            Client::class,
            function (MockInterface $mock) {
                return $mock
                    ->shouldReceive('get')
                    ->andReturn($this->mockedApiData);
            }
        );
    }

    private function getMockedApiData(): array
    {
        return $this->mockedApiData;
    }
}