<?php

namespace Tests\Unit\app\Helpers\Conditions;

use App\Helpers\Conditions\Clause;
use Illuminate\Support\Arr;
use PHPUnit\Framework\TestCase;

class ClauseTest extends TestCase
{

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testSingleClause()
    {
        $clause = new Clause('A', '=', 1);

        // The clause data contains a full OR clause, so for the AND clause we have to flatten it by one
        $expect = Arr::flatten(ClauseData::arraySimpleSingleClause(), 1);

        $this->assertEquals($expect, $clause->toArray());
    }

    public function testSimpleAndClause()
    {
        $clause  = new Clause('A', Clause::EQ, 1);
        $clauseB = new Clause('B', Clause::GT, 1);
        $clause->andClause($clauseB);

        // The clause data contains a full OR clause, so for the AND clause we have to flatten it by one
        $expect = Arr::flatten(ClauseData::arraySimpleAndClause(), 1);

        $this->assertEquals($expect, $clause->toArray());
    }
}
