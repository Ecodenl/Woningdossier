<?php

namespace App\Services\Models;

use App\Models\CompletedQuestionnaire;
use App\Models\InputSource;
use App\Models\Questionnaire;
use App\Models\User;
use App\Traits\FluentCaller;

class QuestionnaireService
{
    use FluentCaller;

    protected Questionnaire $questionnaire;
    protected User $user;
    protected InputSource $inputSource;

    public function user(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function questionnaire(Questionnaire $questionnaire): self
    {
        $this->questionnaire = $questionnaire;
        return $this;
    }

    public function forInputSource(InputSource $inputSource): self
    {
        $this->inputSource = $inputSource;
        return $this;
    }

    public function completeQuestionnaire()
    {
        CompletedQuestionnaire::updateOrCreate(
            [
                'user_id' => $this->user->id,
                'input_source_id' => $this->inputSource->id,
                'questionnaire_id' => $this->questionnaire->id,
            ]
        );
    }
}