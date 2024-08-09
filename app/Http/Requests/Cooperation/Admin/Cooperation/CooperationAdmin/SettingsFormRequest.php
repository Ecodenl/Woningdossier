<?php

namespace App\Http\Requests\Cooperation\Admin\Cooperation\CooperationAdmin;

use App\Helpers\MediaHelper;
use App\Helpers\Models\CooperationSettingHelper;
use App\Helpers\Str;
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
            'cooperation_settings.' . CooperationSettingHelper::SHORT_VERIFICATION_EMAIL_TEXT => [
                'nullable', 'string', function ($attribute, $value, $fail) {
                    if (! Str::contains($value, ':verify_link')) {
                        $fail('Tekst moet ":verify_link" bevatten!');
                    } elseif (Str::substrCount($value, ':verify_link') > 1) {
                        $fail('Tekst mag maar 1 keer ":verify_link" bevatten!');
                    }
                }
            ],
        ];

        foreach (MediaHelper::getFillableTagsForClass(Cooperation::class) as $tag) {
            $rules["medias.{$tag}"] = ['nullable', 'image',  'max:' . MediaHelper::getMaxFileSize($tag)];
        }

        return $rules;
    }
}
