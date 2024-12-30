<?php

namespace App\Helpers\Cooperation\Tool;

use App\Helpers\Conditions\Clause;
use App\Helpers\Conditions\ConditionEvaluator;
use App\Models\InputSource;
use App\Models\SubStep;
use App\Models\User;
use App\Traits\FluentCaller;
use App\Traits\RetrievesAnswers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

abstract class ToolHelper
{
    use RetrievesAnswers,
        FluentCaller;

    /** @var User */
    public $user;

    public InputSource $masterInputSource;

    public bool $withOldAdvices = true;

    /**
     * What values the controller expects.
     *
     * @var array
     */
    public $values;

    public function __construct(User $user, InputSource $inputSource)
    {
        $this->user = $user;
        $this->inputSource = $inputSource;
        $this->building = $user->building;
        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
    }

    public function withoutOldAdvices(): ToolHelper
    {
        $this->withOldAdvices = false;

        return $this;
    }

    /**
     * Simple method to determine whether we should check the old advices.
     *
     * @return bool
     */
    public function shouldCheckOldAdvices(): bool
    {
        return $this->withOldAdvices;
    }

    public function setValues(array $values)
    {
        $this->values = $values;

        return $this;
    }

    /**
     * Get the values or a direct value from the value array.
     *
     *
     * @param null $key
     *
     * @return array|\ArrayAccess|mixed
     */
    public function getValues($key = null)
    {
        if (is_null($key)) {
            return $this->values;
        }

        return Arr::get($this->values, $key);
    }

    // check whether the user considers something, this checks the set values from the $values property
    // so no, its not the same as $user->considers(), and should also be avoided in these helpers.
    public function considers(Model $model): bool
    {
        $considers = $this->getValues("considerables.{$model->id}.is_considering");

        // When not set, it will be null. Not set = considering by default.
        // Almost impossible to happen as the $user->considers() method already returns a default but a fallback is never bad.
        if (is_null($considers)) {
            $considers = true;
        }
        return $considers;
    }

    public function considersByConditions(array $conditions): bool
    {
        return ConditionEvaluator::init()
            ->building($this->building)
            ->inputSource($this->masterInputSource)
            ->evaluate($conditions);
    }

    protected function getConditionConsiderable(string $short): array
    {
        $conditions =  [
            [
                [
                    'column' => 'fn',
                    'operator' => 'HasCompletedStep',
                    'value' => [
                        'steps' => ['heating'],
                        'input_source_shorts' => [
                            InputSource::RESIDENT_SHORT,
                            InputSource::COACH_SHORT,
                        ],
                    ],
                ],
                [
                    [
                        'column' => 'new-heat-source',
                        'operator' => Clause::CONTAINS,
                        'value' => $short,
                    ],
                    [
                        'column' => 'new-heat-source-warm-tap-water',
                        'operator' => Clause::CONTAINS,
                        'value' => $short,
                    ],
                ],
            ],
            [
                [
                    'column' => 'fn',
                    'operator' => 'HasCompletedStep',
                    'value' => [
                        'steps' => ['heating'],
                        'input_source_shorts' => [
                            InputSource::RESIDENT_SHORT,
                            InputSource::COACH_SHORT,
                        ],
                        'should_pass' => false,
                    ],
                ],
                [
                    [
                        'column' => 'heat-source',
                        'operator' => Clause::CONTAINS,
                        'value' => $short,
                    ],
                    [
                        'column' => 'heat-source-warm-tap-water',
                        'operator' => Clause::CONTAINS,
                        'value' => $short,
                    ],
                ],
            ],
        ];

        // We can never have nice things
        if ($short === 'heat-pump') {
            $conditions[] = [
                [
                    'column' => 'fn',
                    'operator' => 'HasCompletedStep',
                    'value' => [
                        'steps' => ['heating'],
                        'input_source_shorts' => [
                            InputSource::RESIDENT_SHORT,
                            InputSource::COACH_SHORT,
                        ],
                        'should_pass' => false,
                    ],
                ],
                [
                    'column' => [
                        'slug->nl' => 'warmtepomp-interesse',
                    ],
                    'operator' => Clause::PASSES,
                    'value' => SubStep::class,
                ],
                [
                    'column' => 'interested-in-heat-pump',
                    'operator' => Clause::EQ,
                    'value' => 'yes',
                ],
            ];
        }

        return $conditions;
    }

    /**
     * Must set the values, according to the given user and InputSource.
     */
    abstract public function createValues(): ToolHelper;

    /**
     * Must save the step data.
     */
    abstract public function saveValues(): ToolHelper;

    /**
     * Must clear and create the user action plan advices.
     */
    abstract public function createAdvices(): ToolHelper;
}
