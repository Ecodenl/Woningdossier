<?php

namespace App\Services\Lvbag\Payloads;

use App\Helpers\Arr;

class City
{
    public ?array $city;

    public function __construct(?array $city = [])
    {
        $this->city = $city;
    }

    public function municipalityName(): ?string
    {
        // i cant come up with a correct english translation
        // we will keep this as "bronhouders"
        $bronhouders = Arr::get($this->city, '_embedded.bronhouders', []);
        // maybe there can be multiple bronhouders for a city / woonplaats
        // we will take the first one.
        if (!empty($bronhouders)) {
            return array_shift($bronhouders)['naam'];
        }
        return null;
    }
}