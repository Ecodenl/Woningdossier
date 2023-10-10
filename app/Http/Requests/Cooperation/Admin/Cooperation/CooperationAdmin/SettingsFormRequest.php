<?php

namespace App\Http\Requests\Cooperation\Admin\Cooperation\CooperationAdmin;

use App\Helpers\MediaHelper;
use App\Helpers\Models\CooperationSettingHelper;
use App\Models\Cooperation;
use Illuminate\Foundation\Http\FormRequest;

class SettingsFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'cooperation_settings.' . CooperationSettingHelper::SHORT_REGISTER_URL => ['nullable', 'url'],
        ];

        foreach (MediaHelper::getFillableTagsForClass(Cooperation::class) as $tag) {
            $rules["medias.{$tag}"] = 'nullable|image';
        }

        return $rules;
    }
}
