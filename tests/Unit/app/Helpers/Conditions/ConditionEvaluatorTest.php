<?php

namespace Tests\Unit\app\Helpers\Conditions;

use App\Helpers\Conditions\ConditionEvaluator;
use PHPUnit\Framework\TestCase;

class ConditionEvaluatorTest extends TestCase
{

    protected function simpleAnd($correct = true)
    {
        // heat-source contains heat-pump
        // AND
        // cook-type = gas
        return [
            [
                [
                    'column' => 'heat-source',
                    'value'  => ($correct ? 'heat-pump' : 'allesbrander'),
                ],
                [
                    'column'   => 'cook-type',
                    'operator' => '=',
                    'value'    => 'gas',
                ],
            ],
        ];
    }

    protected static function simpleOr($correct = true)
    {
        // heat-source contains heat-pump
        // OR
        // cook-type = gas

        return [
            [
                [
                    'column' => 'heat-source',
                    'value'  => ($correct ? 'heat-pump' : 'allesbrander'),
                ],
            ],
            [
                [
                    'column'   => 'cook-type',
                    'operator' => '=',
                    'value'    => ($correct ? 'gas' : 'electrisch'),
                ],
            ],
        ];
    }


    protected static function combineAndOr()
    {
        return [
            [
                [
                    'column' => 'heat-source',
                    'value'  => 'heat-pump',
                ],
            ],
            [
                [
                    'column' => 'heat-source',
                    'value'  => 'allesbrander',
                ],
                [
                    'column'   => 'cook-type',
                    'operator' => '=',
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

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testSimpleCollectionEvaluation()
    {
        $answers   = $this->getTestCollection();
        $evaluator = new ConditionEvaluator();
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

    public function testComplexerCollectionEvaluation()
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

        foreach($answers as $info) {
            $evaluator = new ConditionEvaluator();
            $evaluated = $evaluator->evaluateCollection(
                $this->combineAndOr(),
                $info[0],
            );
            $this->assertEquals($info[1], $evaluated);
        }
    }
}
