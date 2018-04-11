<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class FloorInsulationFormRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'floor_insulation' => 'required|exists:qualities,id',
            // TODO: check for the value's when the right data is present
            'has_crawlspace' => 'required',
            'crawlspace_access' => 'required',
            'crawlspace_height' => 'required|exists:crawl_space_heights,id',
            'floor_surface' => 'required|numeric'
        ];
    }
}
