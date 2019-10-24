<?php

namespace App\Http\Requests\Cooperation\Tool;

use App\Http\Requests\DecimalReplacementTrait;
use Illuminate\Foundation\Http\FormRequest;

class WallInsulationRequest extends FormRequest
{
    use DecimalReplacementTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::check();
    }

    public function getValidatorInstance()
    {
        $this->decimals(['wall_surface', 'insulation_wall_surface']);

        return parent::getValidatorInstance();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // heeft deze woning spouwmuur / huidige staat
            'element' => 'exists:element_values,id|required',
            // radio buttons
            // is de gevel gestuct of gevefd
            'facade_plastered_painted' => 'required|between:1,3',
            // heeft deze woning een spouwmuur
            'cavity_wall' => 'required|between:0,2',
            // inputs
            'damage_paintwork' => 'exists:facade_damaged_paintworks,id',
            'facade_plastered_surface_id' => 'exists:facade_plastered_surfaces,id',
            'wall_joints' => 'exists:facade_surfaces,id',
            'contaminated_wall_joints' => 'exists:facade_surfaces,id',
            // gevel oppervlakte van de woning
            'wall_surface' => 'nullable|numeric|min:0',
            // te isoleren oppervlakte
            'insulation_wall_surface' => 'nullable|numeric|min:0|needs_to_be_lower_or_same_as:wall_surface',
        ];
    }
}
