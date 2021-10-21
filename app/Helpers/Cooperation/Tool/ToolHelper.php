<?php

namespace App\Helpers\Cooperation\Tool;

use App\Models\InputSource;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

abstract class ToolHelper
{
    /** @var User */
    public $user;

    /** @var InputSource */
    public $inputSource;

    public InputSource $masterInputSource;

    /** @var \App\Models\Building */
    public $building;

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

        // when not set, it will be null. not set = not considering
        // almost impossible to happen as the $user->considers() method already returns a default but a fallback is never bet.
        if (is_null($considers)) {
            $considers = true;
        }
        return $considers;
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
