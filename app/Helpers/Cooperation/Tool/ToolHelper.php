<?php

namespace App\Helpers\Cooperation\Tool;

use App\Models\InputSource;
use Illuminate\Support\Arr;
use App\Models\User;

abstract class ToolHelper
{
    /** @var User $user */
    public $user;

    /** @var InputSource $inputSource */
    public $inputSource;

    /** @var \App\Models\Building $building */
    public $building;

    /**
     * What values the controller expects
     *
     * @var array $values
     */
    public $values;

    public function __construct(User $user, InputSource $inputSource)
    {
        $this->user = $user;
        $this->inputSource = $inputSource;
        $this->building = $user->building;
    }

    public function setValues(array $values)
    {
        $this->values = $values;
    }

    /**
     * Get the values or a direct value from the value array.
     *
     * @param null $key
     * @return array|\ArrayAccess|mixed
     */
    public function getValues($key = null)
    {
        if (is_null($key)) {
            return $this->values;
        }
        return Arr::get($this->values, $key);
    }

    /**
     * Must set the values, according to the given user and InputSource.
     */
    abstract public function createValues();

    /**
     * Must save the step data.
     */
    abstract public function save();

    /**
     * Must clear and create the user action plan advices.
     */
    abstract public function saveAdvices();
}