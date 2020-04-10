<?php

namespace App\Http\Requests\Cooperation\Tool;

use App\Helpers\HoomdossierSession;
use App\Http\Requests\DecimalReplacementTrait;
use App\Models\Element;
use App\Models\InputSource;
use App\Rules\ValidateElementKey;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Validator;

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
        $this->decimals(['building_features.wall_surface', 'building_features.insulation_wall_surface']);

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
            'element' => ['exists:element_values,id', 'required', new ValidateElementKey('wall-insulation')],
            // radio buttons
            // is de gevel gestuct of gevefd
            'building_features.facade_plastered_painted' => 'required|between:1,3',
            // heeft deze woning een spouwmuur
            'building_features.cavity_wall' => 'required|between:0,2',
            // inputs
            'building_features.damage_paintwork' => 'exists:facade_damaged_paintworks,id',
            'building_features.facade_plastered_surface_id' => 'exists:facade_plastered_surfaces,id',
            'building_features.wall_joints' => 'exists:facade_surfaces,id',
            'building_features.contaminated_wall_joints' => 'exists:facade_surfaces,id',
            // gevel oppervlakte van de woning
            'building_features.wall_surface' => 'required|numeric|min:1|max:100000',
            // te isoleren oppervlakte
            'building_features.insulation_wall_surface' => 'required|numeric|min:0|needs_to_be_lower_or_same_as:building_features.wall_surface',
        ];
    }

}
