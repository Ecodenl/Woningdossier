<?php

namespace App\Helpers\Api;

use App\Models\ToolQuestion;
use Illuminate\Support\Facades\Log;

class RegisterHelper
{
    /**
     * The tool questions (shorts) that are allowed to be filled upon register.
     * Note: When changing this, ensure you update the Swagger Docs in the Register Controller and the related tests!
     * @var array
     */
    const SUPPORTED_TOOL_QUESTIONS = [
        'amount-gas',
        'amount-electricity',
        'resident-count',
    ];

    /**
     * Convert the shorts into validation.
     *
     * @param  bool  $nullable  Replace required with nullable
     *
     * @return array
     */
    public static function getQuestionValidation(bool $nullable = false): array
    {

        $validation = [];
        foreach (static::SUPPORTED_TOOL_QUESTIONS as $short) {
            $toolQuestion = ToolQuestion::findByShort($short);
            if ($toolQuestion instanceof ToolQuestion) {
                $questionValidation = $toolQuestion->validation;

                if ($nullable && in_array('required', $questionValidation)) {
                    $index = array_search('required', $questionValidation);

                    if ($index !== false) {
                        $questionValidation[$index] = 'nullable';
                    }
                }

                $validation[$short] = $questionValidation;
            } else {
                Log::alert("Tool Question with short {$short} not found!");
            }
        }

        return $validation;
    }
}