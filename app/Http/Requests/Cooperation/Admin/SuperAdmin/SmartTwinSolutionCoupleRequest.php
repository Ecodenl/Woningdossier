<?php

namespace App\Http\Requests\Cooperation\Admin\SuperAdmin;

use App\Helpers\Hoomdossier;
use App\Http\Controllers\Cooperation\Admin\SuperAdmin\SmartTwinSolutionController;
use App\Models\MeasureApplication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SmartTwinSolutionCoupleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Hoomdossier::user()->hasRoleAndIsCurrentRole('super-admin');
    }

    /**
     * An unpicked select submits an empty string, which the controller reads as "nobody has decided
     * yet" — a meaningful state, distinct from "deliberately not coupled". Casting keeps it a string
     * so the rules below can allow it explicitly rather than through `nullable`.
     */
    public function prepareForValidation(): void
    {
        $couplings = $this->input('couplings');

        if (is_array($couplings)) {
            $this->merge(['couplings' => array_map(fn ($choice) => (string) $choice, $couplings)]);
        }
    }

    /**
     * The whole catalogue is submitted at once, so the keys are imported solutions and the values
     * are either a measure application, the marker for "deliberately not coupled", or empty.
     */
    public function rules(): array
    {
        $allowed = array_merge(
            ['', SmartTwinSolutionController::NOT_COUPLED],
            MeasureApplication::pluck('id')->map(fn ($id) => (string) $id)->all(),
        );

        return [
            'couplings' => ['nullable', 'array'],
            'couplings.*' => ['string', Rule::in($allowed)],
        ];
    }
}
