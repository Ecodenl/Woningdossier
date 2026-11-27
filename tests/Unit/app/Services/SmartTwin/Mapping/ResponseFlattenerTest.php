<?php

namespace Tests\Unit\app\Services\SmartTwin\Mapping;

use App\Services\SmartTwin\Mapping\Leaf;
use App\Services\SmartTwin\Mapping\ResponseFlattener;
use PHPUnit\Framework\TestCase;

final class ResponseFlattenerTest extends TestCase
{
    private ResponseFlattener $flattener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->flattener = new ResponseFlattener();
    }

    /** @return array<string, Leaf> */
    private function byPath(array $response): array
    {
        $leaves = [];
        foreach ($this->flattener->flatten($response) as $leaf) {
            $leaves[$leaf->path] = $leaf;
        }

        return $leaves;
    }

    public function test_nested_objects_become_dotted_paths(): void
    {
        $leaves = $this->byPath([
            'current' => ['calculationResult' => ['energyLabel' => 'C']],
        ]);

        $this->assertSame(['current.calculationResult.energyLabel'], array_keys($leaves));
        $this->assertSame('C', $leaves['current.calculationResult.energyLabel']->value);
        $this->assertSame('energyLabel', $leaves['current.calculationResult.energyLabel']->key());
    }

    public function test_list_indexes_collapse_into_a_single_path_group(): void
    {
        $leaves = $this->byPath([
            'solutions' => [
                ['id' => 'a'],
                ['id' => 'b'],
            ],
        ]);

        $this->assertSame(['solutions.0.id', 'solutions.1.id'], array_keys($leaves));

        // The point of the path group: two leaves, one mapping decision.
        foreach ($leaves as $leaf) {
            $this->assertSame('solutions.*.id', $leaf->pathGroup);
        }
    }

    public function test_an_empty_list_is_reported_rather_than_dropped(): void
    {
        $leaves = $this->byPath(['current' => ['roofAssemblies' => []]]);

        $this->assertArrayHasKey('current.roofAssemblies', $leaves);
        $this->assertSame('array', $leaves['current.roofAssemblies']->dataType());
        $this->assertSame('[]', $leaves['current.roofAssemblies']->displayValue());
    }

    public function test_data_types_and_display_values_survive_the_walk(): void
    {
        $leaves = $this->byPath([
            'string' => 'C',
            'int'    => 3,
            'float'  => 2.5,
            'true'   => true,
            'false'  => false,
            'null'   => null,
        ]);

        $this->assertSame('string', $leaves['string']->dataType());
        $this->assertSame('int', $leaves['int']->dataType());
        $this->assertSame('float', $leaves['float']->dataType());
        $this->assertSame('bool', $leaves['true']->dataType());
        $this->assertSame('null', $leaves['null']->dataType());

        // Without this, false and null both render as an empty cell and become indistinguishable.
        $this->assertSame('true', $leaves['true']->displayValue());
        $this->assertSame('false', $leaves['false']->displayValue());
        $this->assertSame('', $leaves['null']->displayValue());
    }

    public function test_every_leaf_of_a_nested_response_is_reported_once(): void
    {
        $leaves = $this->flattener->flatten([
            'current'  => [
                'properties' => [
                    'roofAssemblies' => [
                        ['area' => 10.0, 'roofs' => [['rcValue' => 1.3], ['rcValue' => 2.1]]],
                    ],
                ],
            ],
            'scenario' => ['totalCostWithTax' => 1000],
        ]);

        $this->assertCount(4, $leaves);
    }
}
