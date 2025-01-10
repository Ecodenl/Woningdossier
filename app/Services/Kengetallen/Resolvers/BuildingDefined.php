<?php

namespace App\Services\Kengetallen\Resolvers;

use App\Models\ToolQuestion;

class BuildingDefined extends KengetallenDefiner
{
    public function get(string $kengetallenCode): ?float
    {
        $building = $this->context['building'];
        $inputSource = $this->context['inputSource'];

        if ($kengetallenCode === 'EURO_SAVINGS_GAS') {
            return $building->getAnswer($inputSource, ToolQuestion::findByShort('gas-price-euro'));
        }

        if ($kengetallenCode === 'EURO_SAVINGS_ELECTRICITY') {
            return $building->getAnswer($inputSource, ToolQuestion::findByShort('electricity-price-euro'));
        }
    }
}
