<?php

namespace App\Http\Requests;

use App\Helpers\HoomdossierSession;
use App\Models\User;
use Illuminate\Validation\Factory;

trait ValidatorTrait
{

    /**
     * The rules that apply when a user (mostly a coach), is filling the tool for a user (mostly a resident).
     *
     * @return array
     */
    public function isFillingToolForUserRules(): array
    {
        return [];
    }

    /**
     * Return the rules for the current request.
     *
     * @return array
     */
    private function getRulesForRequest(): array
    {
        return \Auth::user()->isFillingToolForOtherBuilding() ? $this->isFillingToolForUserRules() : $this->all();
    }

    /**
     * Validate the request.
     *
     * @param  Factory  $factory
     *
     * @return \Illuminate\Validation\Validator|null
     */
    public function validator(Factory $factory)
    {
        return $factory->make(
            $this->all(), $this->getRulesForRequest(), $this->messages(), $this->attributes()
        );
    }
}
