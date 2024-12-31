<?php

namespace Tests\Unit\app\Helpers\Conditions;

use App\Helpers\Conditions\Clause;
use App\Helpers\Conditions\ConditionEvaluator;
use PHPUnit\Framework\TestCase;

final class ConditionEvaluatorTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_simple_collection_evaluation(): void
    {
        $answers   = $this->getTestCollection();
        $evaluator = ConditionEvaluator::init();
        $evaluated = $evaluator->setAnswers($answers)->evaluate(
            $this->simpleAnd()
        );
        $this->assertTrue($evaluated);

        $evaluated = $evaluator->setAnswers($answers)->evaluate(
            $this->simpleAnd(false)
        );
        $this->assertFalse($evaluated);

        $evaluated = $evaluator->setAnswers($answers)->evaluate(
            $this->complexAnd(true, false)
        );
        $this->assertTrue($evaluated);

        $evaluated = $evaluator->setAnswers($answers)->evaluate(
            $this->complexAnd(true, true)
        );
        $this->assertTrue($evaluated);

        $evaluated = $evaluator->setAnswers($answers)->evaluate(
            $this->complexAnd(false, true)
        );
        $this->assertTrue($evaluated);

        $evaluated = $evaluator->setAnswers($answers)->evaluate(
            $this->complexAnd(false, false)
        );
        $this->assertFalse($evaluated);

        $evaluated = $evaluator->setAnswers($answers)->evaluate(
            $this->simpleOr()
        );
        $this->assertTrue($evaluated);

        $evaluated = $evaluator->setAnswers($answers)->evaluate(
            $this->simpleOr(false)
        );
        $this->assertFalse($evaluated);
    }

    public function test_complexer_collection_evaluation(): void
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

        foreach ($answers as $info) {
            $evaluated = $evaluator->setAnswers($info[0])->evaluate(
                $this->combineAndOr()
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
