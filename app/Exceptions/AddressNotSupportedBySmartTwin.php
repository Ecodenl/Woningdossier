<?php

namespace App\Exceptions;

use App\Models\Building;
use App\Models\Cooperation;
use Exception;

/**
 * SmartTwin's Building Data API answered that it does not support an address (isSupported false):
 * Twee Snoeken, who calculate SmartTwin's advice, cannot work with it. Reported, not thrown, so it
 * shows up in Sentry with the address and the cooperation to take up with SmartTwin.
 */
class AddressNotSupportedBySmartTwin extends Exception
{
    public function __construct(public readonly Building $building, public readonly ?Cooperation $cooperation)
    {
        $address = trim("{$building->postal_code} {$building->number} {$building->extension}");
        $cooperationName = $cooperation->name ?? 'no cooperation';

        parent::__construct("SmartTwin does not support the address {$address} (cooperation {$cooperationName})");
    }

    /**
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return [
            'building_id'    => $this->building->id,
            'postal_code'    => $this->building->postal_code,
            'number'         => $this->building->number,
            'extension'      => $this->building->extension,
            'cooperation_id' => $this->cooperation?->id,
            'cooperation'    => $this->cooperation?->name,
        ];
    }
}
