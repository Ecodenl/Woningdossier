<?php

namespace App\Livewire\Cooperation\Admin\Cooperation\CooperationAdmin\Questionnaires;

use App\Helpers\Arr;
use App\Helpers\Str;
use App\Models\Cooperation;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\QuestionOption;
use App\Rules\LanguageRequired;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Features\SupportRedirects\Redirector;

class FormBuilder extends Component
{
    public Cooperation $cooperation;
    public Questionnaire $questionnaire;
    public array $questions = [];

    protected function rules(): array
    {
        $questionTypes = array_keys(__('cooperation/admin/cooperation/cooperation-admin/questionnaires.edit.types'));
        //$validationTypes = array_keys(__('cooperation/admin/cooperation/cooperation-admin/questionnaires.form-builder.rules'));
        //$validationRules = array_merge(
        //    array_keys(__('cooperation/admin/cooperation/cooperation-admin/questionnaires.form-builder.optional-rules.numeric')),
        //    array_keys(__('cooperation/admin/cooperation/cooperation-admin/questionnaires.form-builder.optional-rules.string'))
        //);

        return [
            'questions' => [
                'nullable',
                'array',
            ],
            'questions.*.type' => [
                'required',
                'in:' . implode(',', $questionTypes),
            ],
            'questions.*.name' => [
                new LanguageRequired(),
            ],
            'questions.*.required' => [
                'required',
                'boolean',
            ],
            'questions.*.options' => [
                'nullable',
                'array',
                // Sadly doesn't work, see boot
                //'required_if:questions.*.type,in:select,radio,checkbox',
            ],
            'questions.*.options.*.name' => [
                new LanguageRequired(),
            ],
            'questions.*.validation' => ['nullable', 'array'],
            // Same as above, doesn't work without required_if which doesn't work, see boot.
            //'questions.*.validation.type' => [
            //    'required',
            //    'in:' . implode(',', $validationTypes),
            //],
            //'questions.*.validation.rule' => [
            //    'required',
            //    'in:' . implode(',', $validationRules),
            //],
            //'questions.*.validation.min' => ['required', 'integer'],
            //'questions.*.validation.max' => ['required', 'integer'],
        ];
    }

    protected function validationAttributes(): array
    {
        $base = [
            'questions.*.type' => 'vraagsoort',
            'questions.*.name' => 'naam',
            'questions.*.required' => 'verplicht',
            'questions.*.options' => 'opties',
            'questions.*.options.*.name' => 'optie',
            'questions.*.validation' => 'validatie',
            'questions.*.validation.type' => 'soort validatieregel',
            'questions.*.validation.rule' => 'validatieregel',
            'questions.*.validation.min' => 'min',
            'questions.*.validation.max' => 'max',
        ];

        $translations = [
            'questions' => 'vragen',
        ];

        foreach ($this->questions as $order => $question) {
            foreach ($base as $key => $trans) {
                $key = Str::replaceFirst('*', $order, $key);

                if (str_contains($key, 'options.*')) {
                    foreach (data_get($question, 'options', []) as $optionOrder => $option) {
                        $newKey = Str::replaceFirst('*', $optionOrder, $key);
                        $translations[$newKey] = $trans;
                    }
                } else {
                    $translations[$key] = $trans;
                }
            }
        }

        return $translations;
    }

    public function mount(Questionnaire $questionnaire): void
    {
        /** @phpstan-ignore argument.type */
        $this->questions = $questionnaire->questions()->with('questionOptions')->get()->map(function (Question $question) {
            // Deconstruct (legacy) validation syntax.
            $validation = $question->validation;
            if (! empty($validation)) {
                $type = array_key_first($validation);
                $rule = array_key_first($validation[$type]);
                $validation = [
                    'type' => $type,
                    'rule' => $rule,
                    'min' => $validation[$type][$rule][0] ?? null,
                    'max' => $validation[$type][$rule][1] ?? null,
                ];

                if ($rule === 'max') {
                    $validation['max'] = $validation['min'];
                    $validation['min'] = null;
                }
            }

            return [
                'id' => $question->id,
                'key' => 'question-' . $question->id,
                'type' => $question->type,
                'required' => $question->required,
                'validation' => $validation,
                'name' => $question->getTranslations('name'),
                'options' => $question->questionOptions->map(function (QuestionOption $questionOption) {
                    return [
                        'id' => $questionOption->id,
                        'key' => 'option-' . $questionOption->id,
                        'name' => $questionOption->getTranslations('name'),
                    ];
                })->all(),
            ];
        })->all();
    }

    public function boot(): void
    {
        $this->withValidator(function ($validator) {
            $validator->after(function ($validator) {
                foreach ($this->questions as $order => $question) {
                    if (in_array($question['type'], ['select', 'radio', 'checkbox']) && empty($question['options'])) {
                        $validator->errors()->add("questions.{$order}.options", __('validation.min.array', ['attribute' => 'opties', 'min' => 1]));
                    }
                    if (in_array($question['type'], ['text', 'textarea']) && ! empty($question['validation'])) {
                        if ($question['validation']['rule'] === 'between' && $question['validation']['max'] <= $question['validation']['min']) {
                            $validator->errors()->add("questions.{$order}.validation.max", __('validation.gte.numeric', ['attribute' => 'max', 'value' => 'min']));
                        }
                    }
                }
            });

            $validationTypes = array_keys(__('cooperation/admin/cooperation/cooperation-admin/questionnaires.form-builder.rules'));
            $validationRules = array_merge(
                array_keys(__('cooperation/admin/cooperation/cooperation-admin/questionnaires.form-builder.optional-rules.numeric')),
                array_keys(__('cooperation/admin/cooperation/cooperation-admin/questionnaires.form-builder.optional-rules.string'))
            );

            $questionInputValidationRules = [
                'questions.*.validation.rule' => [
                    'required',
                    'in:' . implode(',', $validationRules),
                ],
                'questions.*.validation.type' => [
                    'required',
                    'in:' . implode(',', $validationTypes),
                ],
                'questions.*.validation.min' => ['required', 'integer', 'min:1'],
                'questions.*.validation.max' => ['required', 'integer', 'min:1'],
            ];

            foreach ($this->questions as $order => $question) {
                foreach ($questionInputValidationRules as $field => $rules) {
                    $field = str_replace('*', $order, $field);
                    $validator->sometimes($field, $rules, function () use ($field, $question) {
                        $base = in_array($question['type'], ['text', 'textarea']) && ! empty($question['validation']);
                        // Quit early
                        if (! $base) {
                            return false;
                        }

                        if (str_ends_with($field, 'min')) {
                            return data_get($question, 'validation.type') === 'numeric';
                        }
                        if (str_ends_with($field, 'max')) {
                            $type = data_get($question, 'validation.type');
                            $rule = data_get($question, 'validation.rule');
                            return ($type === 'numeric' && $rule === 'between') || ($type === 'string' && $rule === 'max');
                        }

                        return true;
                    });
                }
            }
        });
    }

    public function render(): View
    {
        return view('livewire.cooperation.admin.cooperation.cooperation-admin.questionnaires.form-builder');
    }

    public function updated(string $field, mixed $value): void
    {
        if (str_contains($field, 'validation')) {
            $key = Str::of($field)->after('.')->beforeLast('.')->toString();
            $questions = $this->questions;

            if (str_ends_with($field, 'type')) {
                if ($value === 'string') {
                    // We show min if type is numeric, that's no longer the case so we set null.
                    data_set($questions, "{$key}.min", null);
                }

                // New type, so new rule also.
                $newRule = $value === 'string' ? 'email' : 'between';
                data_set($questions, "{$key}.rule", $newRule);
            }
            if (str_ends_with($field, 'rule')) {
                $type = data_get($questions, "{$key}.type");
                if (($type === 'numeric' && $value !== 'between') || ($type === 'string' && $value !== 'max')) {
                    // Max no longer visible, so we set null.
                    data_set($questions, "{$key}.max", null);
                }
            }

            $this->questions = $questions;
        }
    }

    public function addQuestion(string $type): void
    {
        $questions = $this->questions;
        $key = 'new-question-' . Str::random(8);
        array_unshift($questions, [
            'key' => $key,
            'type' => $type,
            'required' => false,
            'validation' => [],
            'name' => [
                'nl' => '',
            ],
            'options' => [],
        ]);
        $this->questions = $questions;

        if (in_array($type, ['select', 'radio', 'checkbox'])) {
            $this->addOption($key);
        }

        $this->resetErrorBag();
    }

    public function removeQuestion(string $key): void
    {
        $questions = $this->questions;
        $index = array_key_first(Arr::where($questions, fn ($v) => $key === $v['key']));
        unset($questions[$index]);
        $this->questions = $questions;

        $this->resetErrorBag();
    }

    public function addOption(string $key): void
    {
        $questions = $this->questions;
        $index = array_key_first(Arr::where($questions, fn ($v) => $key === $v['key']));
        $questions[$index]['options'][] = [
            'key' => 'new-option-' . Str::random(8),
            'name' => ['nl' => ''],
        ];
        $this->questions = $questions;
    }

    public function removeOption(string $key, int $order): void
    {
        $questions = $this->questions;
        $index = array_key_first(Arr::where($questions, fn ($v) => $key === $v['key']));
        unset($questions[$index]['options'][$order]);
        $this->questions = $questions;
    }

    public function addValidation(string $key): void
    {
        $questions = $this->questions;
        $index = array_key_first(Arr::where($questions, fn ($v) => $key === $v['key']));
        $questions[$index]['validation'] = [
            'type' => 'numeric',
            'rule' => 'between',
            'min' => null,
            'max' => null,
        ];
        $this->questions = $questions;
    }

    public function removeValidation(string $key): void
    {
        $questions = $this->questions;
        $index = array_key_first(Arr::where($questions, fn ($v) => $key === $v['key']));
        $questions[$index]['validation'] = [];
        $this->questions = $questions;
    }

    public function moveUp(string $key): void
    {
        $questions = $this->questions;
        $data = Arr::where($questions, fn ($v) => $key === $v['key']);
        $index = array_key_first($data);
        $question = $data[$index];
        if ($index !== 0 && count($questions) > 1) {
            $questions[$index] = $questions[$index - 1];
            $questions[$index - 1] = $question;
        }
        $this->questions = $questions;
    }

    public function moveDown(string $key): void
    {
        $questions = $this->questions;
        $data = Arr::where($questions, fn ($v) => $key === $v['key']);
        $index = array_key_first($data);
        $question = $data[$index];
        if ($index !== array_key_last($questions) && count($questions) > 1) {
            $questions[$index] = $questions[$index + 1];
            $questions[$index + 1] = $question;
        }
        $this->questions = $questions;
    }

    public function save(): Redirector|RedirectResponse
    {
        $this->validate();

        $ids = array_filter(Arr::pluck($this->questions, 'id'));
        // Delete removed questions. They are soft delete, so no need to worry about question options.
        /** @phpstan-ignore argument.type */
        $this->questionnaire->questions()->whereNotIn('id', $ids)->eachById(fn (Question $question) => $question->delete());

        foreach ($this->questions as $order => $questionData) {
            $validation = [];
            // Build (legacy) validation structure
            if (! empty($questionData['validation'])) {
                $validation = [
                    $questionData['validation']['type'] => [
                        $questionData['validation']['rule'] => array_values(array_filter([
                            $questionData['validation']['min'],
                            $questionData['validation']['max'],
                        ])),
                    ],
                ];
            }

            $question = null;
            // If there's an ID, it was pulled from the database, and so we want to ensure we update it.
            if (! empty($questionData['id'])) {
                $question = $this->questionnaire->questions()->find($questionData['id']);
                if ($question instanceof Question) {
                    $question->update([
                        'name' => $questionData['name'],
                        'type' => $questionData['type'],
                        'order' => $order,
                        'required' => (bool) $questionData['required'],
                        'validation' => $validation,
                    ]);
                }
            }

            if (! $question instanceof Question) {
                $question = $this->questionnaire->questions()->create([
                    'name' => $questionData['name'],
                    'type' => $questionData['type'],
                    'order' => $order,
                    'required' => (bool) $questionData['required'],
                    'validation' => $validation,
                ]);
            }

            if (! empty($questionData['options'])) {
                $ids = array_filter(Arr::pluck($questionData['options'], 'id'));
                // Delete removed question options.
                $question->questionOptions()->whereNotIn('id', $ids)->eachById(fn (QuestionOption $question) => $question->delete());

                foreach ($questionData['options'] as $optionData) {
                    $questionOption = null;
                    // If there's an ID, it was pulled from the database, and so we want to ensure we update it.
                    if (! empty($optionData['id'])) {
                        $questionOption = $question->questionOptions()->find($optionData['id']);
                        if ($questionOption instanceof QuestionOption) {
                            $questionOption->update([
                                'name' => $optionData['name'],
                            ]);
                        }
                    }

                    if (! $questionOption instanceof QuestionOption) {
                        $questionOption = $question->questionOptions()->create([
                            'name' => $optionData['name'],
                        ]);
                    }
                }
            }
        }

        return redirect()->route('cooperation.admin.cooperation.questionnaires.index', ['cooperation' => $this->cooperation])
            ->with('success', __('cooperation/admin/cooperation/cooperation-admin/questionnaires.edit.success'));
    }
}
