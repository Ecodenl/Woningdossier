<?php

namespace Tests\Unit\app\Helpers\Api;

use App\Helpers\Api\RegisterHelper;
use App\Models\ToolQuestion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterHelperTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_question_validation()
    {
        foreach (RegisterHelper::SUPPORTED_TOOL_QUESTIONS as $short) {
            factory(ToolQuestion::class)->create([
                'short' => $short,
                'validation' => [
                    'required', 'string',
                ],
                'save_in' => null,
            ]);
        }

        $validation = RegisterHelper::getQuestionValidation();
        $this->assertEquals([
            'amount-gas' => ['required', 'string'],
            'amount-electricity' => ['required', 'string'],
            'resident-count' => ['required', 'string'],
        ], $validation);

        $validation = RegisterHelper::getQuestionValidation(true);
        $this->assertEquals([
            'amount-gas' => ['nullable', 'string'],
            'amount-electricity' => ['nullable', 'string'],
            'resident-count' => ['nullable', 'string'],
        ], $validation);
    }
}