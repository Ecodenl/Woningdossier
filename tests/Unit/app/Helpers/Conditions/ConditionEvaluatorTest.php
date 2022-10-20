<?php

namespace Tests\Unit\app\Helpers\Conditions;

use App\Helpers\Conditions\Clause;
use App\Helpers\Conditions\ConditionEvaluator;
use PHPUnit\Framework\TestCase;

class ConditionEvaluatorTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_simple_collection_evaluation()
    {
        $answers   = $this->getTestCollection();
        $evaluator = ConditionEvaluator::init();
        $evaluated = $evaluator->evaluateCollection(
            $this->simpleAnd(),
            $answers
        );
        $this->assertTrue($evaluated);

        $evaluated = $evaluator->evaluateCollection(
            $this->simpleAnd(false),
            $answers
        );
        $this->assertFalse($evaluated);

        $evaluated = $evaluator->evaluateCollection(
            $this->complexAnd(true, false),
            $answers
        );
        $this->assertTrue($evaluated);

        $evaluated = $evaluator->evaluateCollection(
            $this->complexAnd(true, true),
            $answers
        );
        $this->assertTrue($evaluated);

        $evaluated = $evaluator->evaluateCollection(
            $this->complexAnd(false, true),
            $answers
        );
        $this->assertTrue($evaluated);

        $evaluated = $evaluator->evaluateCollection(
            $this->complexAnd(false, false),
            $answers
        );
        $this->assertFalse($evaluated);

        $evaluated = $evaluator->evaluateCollection(
            $this->simpleOr(),
            $answers
        );
        $this->assertTrue($evaluated);

        $evaluated = $evaluator->evaluateCollection(
            $this->simpleOr(false),
            $answers
        );
        $this->assertFalse($evaluated);
    }

    public function test_complexer_collection_evaluation()
    {
        $answers = [
            [
                // first matches
                collect([
                    "heat-source" => collect([
                        "heat-pump",
                    ]),
                    "cook-type"   => 'electrisch',
                ]),
                true,
            ],
            // second matches
            [
                collect([
                    "heat-source" => collect([
                        "allesbrander",
                    ]),
                    "cook-type"   => 'gas',
                ]),
                true,
            ],
            // both match
            [
                collect([
                    "heat-source" => collect([
                        "heat-pump",
                        "allesbrander",
                    ]),
                    "cook-type"   => 'gas',
                ]),
                true,
            ],
            // none match
            [
                collect([
                    "heat-source" => collect([
                        "hr-boiler",
                    ]),
                    "cook-type"   => 'electrisch',
                ]),
                false,
            ],
        ];

        $evaluator = ConditionEvaluator::init();

        foreach($answers as $info) {
            $evaluated = $evaluator->evaluateCollection(
                $this->combineAndOr(),
                $info[0],
            );
            $this->assertEquals($info[1], $evaluated);
        }
    }

    protected function simpleAnd($correct = true)
    {
        // heat-source contains heat-pump
        // AND
        // cook-type = gas
        return [
            [
                [
                    'column' => 'heat-source',
                    'operator' => Clause::CONTAINS,
                    'value'  => ($correct ? 'heat-pump' : 'allesbrander'),
                ],
                [
                    'column'   => 'cook-type',
                    'operator' => Clause::EQ,
                    'value'    => 'gas',
                ],
            ],
        ];
    }

    protected function complexAnd($firstCorrect = true, $secondCorrect = false)
    {
        // heat-source contains heat-pump OR hr-boiler
        // AND
        // cook-type = gas
        return [
            [
                [
                    [
                        'column' => 'heat-source',
                        'operator' => Clause::CONTAINS,
                        'value'  => ($firstCorrect ? 'heat-pump' : 'allesbrander'),
                    ],
                    [
                        'column' => 'heat-source',
                        'operator' => Clause::CONTAINS,
                        'value'  => ($secondCorrect ? 'hr-boiler' : 'gaskanon'),
                    ],
                ],
                [
                    'column'   => 'cook-type',
                    'operator' => Clause::EQ,
                    'value'    => 'gas',
                ],
            ],
        ];
    }


    protected function simpleOr($correct = true)
    {
        // heat-source contains heat-pump
        // OR
        // cook-type = gas

        return [
            [
                [
                    'column' => 'heat-source',
                    'operator' => Clause::CONTAINS,
                    'value'  => ($correct ? 'heat-pump' : 'allesbrander'),
                ],
            ],
            [
                [
                    'column'   => 'cook-type',
                    'operator' => Clause::EQ,
                    'value'    => ($correct ? 'gas' : 'electrisch'),
                ],
            ],
        ];
    }

    protected function combineAndOr()
    {
        return [
            [
                [
                    'column' => 'heat-source',
                    'operator' => Clause::CONTAINS,
                    'value'  => 'heat-pump',
                ],
            ],
            [
                [
                    'column' => 'heat-source',
                    'operator' => Clause::CONTAINS,
                    'value'  => 'allesbrander',
                ],
                [
                    'column'   => 'cook-type',
                    'operator' => Clause::EQ,
                    'value'    => 'gas',
                ],
            ],
        ];
    }

    protected function getTestCollection()
    {
        return collect([
            "heat-source" => collect([
                "hr-boiler",
                "heat-pump",
            ]),
            "cook-type"   => 'gas',
        ]);
    }
}
