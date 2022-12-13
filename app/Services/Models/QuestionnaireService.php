<?php

namespace App\Services\Models;

use App\Models\CompletedQuestionnaire;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Questionnaire;
use App\Models\Step;
use App\Models\User;
use App\Traits\FluentCaller;
use Illuminate\Support\Collection;

class QuestionnaireService
{
    use FluentCaller;

    protected ?Questionnaire $questionnaire = null;
    protected Cooperation $cooperation;
    protected Step $step;
    protected User $user;
    protected InputSource $inputSource;

    public function questionnaire(Questionnaire $questionnaire): self
    {
        $this->questionnaire = $questionnaire;
        return $this;
    }

    public function cooperation(Cooperation $cooperation): self
    {
        $this->cooperation = $cooperation;
        return $this;
    }

    public function user(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function step(Step $step): self
    {
        $this->step = $step;
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

    public function getQuestionnaires(): Collection
    {
        return $this->step->questionnaires()
            ->where('cooperation_id', $this->cooperation->id)
            ->orderByPivot('order')
            ->get();
    }

    public function hasActiveQuestionnaires(): bool
    {
        return $this->step->questionnaires()
            ->active()
            ->where('cooperation_id', $this->cooperation->id)
            ->count() > 0;
    }

    public function resolveQuestionnaire(bool $next = true): ?Questionnaire
    {
        $query = $this->step->questionnaires()
            ->active()
            ->where('cooperation_id', $this->cooperation->id)
            ->orderByPivot('order', $next ? 'asc' : 'desc');

        if ($this->questionnaire instanceof Questionnaire) {
            $query->wherePivot('order', $next ? '>' : '<', $this->questionnaire->questionnaireSteps()->where('step_id', $this->step->id)->first()->order);
        }

        return $query->first();
    }
}