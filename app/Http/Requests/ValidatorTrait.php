<?php

namespace App\Http\Requests;

use App\Helpers\HoomdossierSession;
use Illuminate\Validation\Factory;

trait ValidatorTrait
{
    public $alwaysValidate = false;

    /**
     * Validate the request.
     *
     * @param  Factory  $factory
     *
     * @return \Illuminate\Validation\Validator
     */
    public function validator(Factory $factory)
    {
        $validate = null;

        if ($this->alwaysValidate) {
            // validate it like it normaly would
            $validate = $factory->make(
                $this->all(), $this->container->call([$this, 'rules']), $this->messages(), $this->attributes()
            );

        } else {
            // if the session set building != to the Auth user his building, then the Auth user is probably filling the tool for a resident
            // we don't validate inputs if so
            if (HoomdossierSession::getBuilding() != \Auth::user()->buildings()->first()->id) {
                // pass empty values and rules so the validation will always pass
                $validate = $factory->make([], []);
            }
        }



        return $validate;
    }
}
