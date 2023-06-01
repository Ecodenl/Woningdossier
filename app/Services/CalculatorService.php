<?php

namespace App\Services;

use App\Helpers\KengetallenCodes;
use App\Helpers\RawCalculator;
use App\Services\Kengetallen\KengetallenService;
use App\Traits\Services\HasBuilding;
use App\Traits\Services\HasInputSources;

class CalculatorService
{
    use HasBuilding, HasInputSources;

    public KengetallenService $kengetallenService;

    public function __construct(KengetallenService $kengetallenService)
    {
        $this->kengetallenService = $kengetallenService;
    }

    private function resolveKengetal(string $kengetalCode)
    {
        return $this
            ->kengetallenService
            ->forBuilding($this->building)
            ->forInputSource($this->inputSource ?? $this->masterInputSource())
            ->resolve($kengetalCode);
    }

    public function calculateMoneySavings($gasSavings)
    {
        return RawCalculator::calculateMoneySavings(
            $gasSavings,
            $this->resolveKengetal(KengetallenCodes::EURO_SAVINGS_GAS)
        );
    }
}

