<?php

namespace App\Livewire\Cooperation\Admin\Cooperation\CooperationAdmin\Questionnaires;

use App\Helpers\Arr;
use App\Helpers\Str;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\QuestionOption;
use Livewire\Component;

class FormBuilder extends Component
{
    public Questionnaire $questionnaire;
    public array $questions = [];

    public function mount(Questionnaire $questionnaire)
    {
        $this->questions = $questionnaire->questions()->with('questionOptions')->get()->map(function (Question $question) {
            return [
                'id' => $question->id,
                'key' => 'question-' . $question->id,
                'type' => $question->type,
                'required' => $question->required,
                'validation' => $question->validation,
                'name' => $question->getTranslations('name'),
                'options' => $question->questionOptions->map(function (QuestionOption $questionOption) {
                    return ['name' => $questionOption->getTranslations('name')];
                })->all(),
            ];
        })->all();
    }

    public function render()
    {
        return view('livewire.cooperation.admin.cooperation.cooperation-admin.questionnaires.form-builder');
    }

    public function addQuestion(string $type): void
    {
        $questions = $this->questions;
        array_unshift($questions, [
            'key' => 'new-question-' . Str::random(8),
            'type' => $type,
            'required' => false,
            'validation' => [],
            'name' => [
                'nl' => '',
            ],
            'options' => [],
        ]);
        $this->questions = $questions;
    }

    public function removeQuestion(string $key): void
    {
        $questions = $this->questions;
        $index = array_key_first(Arr::where($questions, fn ($v) => $key === $v['key']));
        unset($questions[$index]);
        $this->questions = $questions;
    }
}
