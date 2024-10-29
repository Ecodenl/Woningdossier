<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class MaxFilenameLength implements ValidationRule
{
    protected int $maxLength = 255;

    public function __construct(int $maxLength = 255)
    {
        $this->maxLength = $maxLength;
    }

    public function passes($attribute, $value)
    {
        if ($value instanceof UploadedFile) {
            // Client original name contains file extension also
            return strlen($value->getClientOriginalName()) <= $this->maxLength;
        }

        return true;
    }

    public function message()
    {
        return __('validation.custom-rules.max-filename-length', [
            'length' => $this->maxLength,
        ]);
    }

    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }
}
