<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class InsulatedGlazingFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {

        // Collect the main "question" keys
        $insultedGlazings = [
            'glass-in-lead',
            'place-hr-only-glass',
            'place-hr-with-frame',
            'triple-hr-glass',
        ];

        // Then collect the properties off a question
        $insultedGlazingProperties = [
            'current-glass' => 'current-glass',
            'heated-rooms' => 'heated-rooms',
            'm2' => 'm2',
            'total-windows' => 'total-windows',
        ];

        // Collect them and put set some validate rules on them.
        foreach ($insultedGlazings as $insultedGlazing) {
            $validateInsulatedGlazing[$insultedGlazing.'.'.$insultedGlazingProperties['current-glass']] = 'required|exists:insulating_glazings,id';
            $validateInsulatedGlazing[$insultedGlazing.'.'.$insultedGlazingProperties['heated-rooms']] = 'required|exists:insulating_glazings,id';
            $validateInsulatedGlazing[$insultedGlazing.'.'.$insultedGlazingProperties['m2']] = 'required|numeric';
            $validateInsulatedGlazing[$insultedGlazing.'.'.$insultedGlazingProperties['total-windows']] = 'required|numeric';
        }

        return [
            $validateInsulatedGlazing
        ];
    }
}
