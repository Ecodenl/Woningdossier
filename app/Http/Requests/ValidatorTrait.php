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
     * Validate the request.
     *
     * @param  Factory  $factory
     *
     * @return \Illuminate\Validation\Validator|null
     */
    public function validator(Factory $factory)
    {
        $validate = null;

        // if the session set building != to the Auth user his building, then the Auth user is probably filling the tool for a resident
        // we don't validate inputs if so
        if (\Auth::user()->isFillingToolForOtherBuilding()) {
            // pass empty values and rules so the validation will always pass
            $validate = $factory->make($this->all(), $this->isFillingToolForUserRules());
        } else {

            $validate = $factory->make(
                $this->all(), $this->container->call([$this, 'rules']), $this->messages(), $this->attributes()
            );
        }

        return $validate;
    }
}
