<?php

namespace Tests\Unit\app\Helpers\Conditions;

use App\Helpers\Conditions\ClauseBuilder;
use PHPUnit\Framework\TestCase;

class ClauseBuilderTest extends TestCase
{

    /**
     * @return void
     */
    public function testFromArrayInitResultsInSameStructure()
    {
        $arrayForms = [
            ClauseData::arraySimpleSingleClause(),
            ClauseData::arraySimpleAndClause(),
            ClauseData::arrayAndOrClause(),
        ];
        foreach ($arrayForms as $i => $arrayForm) {
            $builder = ClauseBuilder::fromArray($arrayForm);
            $this->assertEquals($arrayForm, $builder->toArray());
        }
    }
}
