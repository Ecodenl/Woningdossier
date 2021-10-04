<div class="w-full space-y-2">
    <div class="w-full mb-4">
        <h4 class="heading-4">
            {{ $subStep->name }}
        </h4>
    </div>

    @php
        $subStepsToSummarize = $step->subSteps()->where('id', '!=', $subStep->id)->orderBy('order')->get();
    @endphp

    {{-- Loop all sub steps except for the current (summary) step --}}
    @foreach($subStepsToSummarize as $subStepToSummarize)
        {{-- Only display sub steps that are valid to the user --}}
        @can('show', $subStepToSummarize)
            @php
                $subStepRoute = route('cooperation.frontend.tool.quick-scan.index', [
                    'cooperation' => $cooperation, 'step' => $step, 'subStep' => $subStepToSummarize
                ]);
            @endphp
            <div class="flex flex-row flex-wrap w-full space-y-2">
                {{-- Custom changes has no tool questions, it's basically a whole other story --}}
                @if($subStepToSummarize->slug === 'welke-zaken-vervangen')
                    <div class="flex flex-row flex-wrap w-full">
                        <div class="w-1/2">
                            <a href="{{ $subStepRoute }}" class="no-underline">
                                <h6 class="as-text font-bold">
                                    @lang('livewire/cooperation/frontend/tool/quick-scan/custom-changes.question.label'):
                                </h6>
                            </a>
                        </div>

                        <div class="w-1/2">
                            <p class="flex items-center">
                                @php $advisables = []; @endphp
                                @foreach($building->user->actionPlanAdvices()->forInputSource($masterInputSource)->get() as $advice)
                                    @php
                                        if ($advice->user_action_plan_advisable_type === \App\Models\CustomMeasureApplication::class) {
                                            $advisable = $advice->userActionPlanAdvisable()
                                                ->forInputSource($this->masterInputSource)
                                                ->first();
                                        } else {
                                            $advisable = $advice->userActionPlanAdvisable;
                                        }

                                        if ($advisable instanceof \App\Models\CustomMeasureApplication) {
                                            $advisables[] = $advisable->name;
                                        } elseif($advisable instanceof \App\Models\CooperationMeasureApplication) {
                                            $advisableToAppend = $advisable->name;

                                            if (! empty($advisable->extra['icon'])) {
                                                $advisableToAppend .= '<i class="ml-1 w-8 h-8 '. $advisable->extra['icon'] . '"></i>';
                                            }

                                            $advisables[] = $advisableToAppend;
                                        }
                                    @endphp
                                @endforeach
                                {!! implode(', ', $advisables) !!}
                            </p>
                        </div>
                    </div>
                @else
                    @php
                        $answers = [];
                    @endphp
                    {{-- We loop twice to first get all answers. We need the answers to ensure whether or not the tool question should be shown --}}
                    @foreach($subStepToSummarize->toolQuestions as $toolQuestionToSummarize)
                        @php
                            // Answers will contain an array of arrays of all answers for the tool question in this sub step,
                            // in which the nested array will be short => answer based
                            $answers[$toolQuestionToSummarize->short] = $building->getAnswer(($toolQuestionToSummarize->forSpecificInputSource ?? $masterInputSource), $toolQuestionToSummarize);
                        @endphp
                    @endforeach

                    @foreach($subStepToSummarize->toolQuestions as $toolQuestionToSummarize)
                        {{-- Only display questions that are valid to the user --}}
                        @php
                            $showQuestion = true;

                            if (! empty($toolQuestionToSummarize->conditions)) {
                                $showQuestion = \App\Helpers\Conditions\ConditionEvaluator::init()
                                ->evaluateCollection($toolQuestionToSummarize->conditions, collect($answers));
                            }

                            // Comments come at the end, and have exceptional logic...
                            if (\Illuminate\Support\Str::contains($toolQuestionToSummarize->short, 'comment')) {
                                $showQuestion = false;
                            }
                        @endphp

                        @if ($showQuestion)
                            <div class="flex flex-row flex-wrap w-full">
                                <div class="@if($toolQuestionToSummarize->toolQuestionType->short === 'rating-slider') w-full @else w-1/2 @endif">
                                    <a href="{{ $subStepRoute }}" class="no-underline">
                                        <h6 class="as-text font-bold">
                                            {{ $toolQuestionToSummarize->name }}:
                                        </h6>
                                    </a>
                                </div>

                                @if($toolQuestionToSummarize->toolQuestionType->short === 'rating-slider')
                                    @foreach($toolQuestionToSummarize->options as $option)
                                        <div class="w-1/2 pl-2">
                                            <a href="{{ $subStepRoute }}" class="no-underline">
                                                <h6 class="as-text font-bold">
                                                    {{ $option['name'] }}:
                                                </h6>
                                            </a>
                                        </div>
                                        <div class="w-1/2">
                                            <p class="flex items-center">
                                                @php
                                                    $humanReadableAnswer = __('cooperation/frontend/tool.no-answer-given');
                                                    $answer = $answers[$toolQuestionToSummarize->short] ?? null;

                                                    if (! empty($answer)) {
                                                        $humanReadableAnswer = json_decode($answer, true)[$option['short']];
                                                    }
                                                @endphp

                                                {{ $humanReadableAnswer }}
                                            </p>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="w-1/2">
                                        <p class="flex items-center">
                                            @php
                                                $humanReadableAnswer = __('cooperation/frontend/tool.no-answer-given');
                                                $answer = $answers[$toolQuestionToSummarize->short] ?? null;

                                                if (! empty($answer) || (is_numeric($answer) && (int) $answer === 0)) {
                                                    $questionValues = $toolQuestionToSummarize->getQuestionValues();

                                                    if ($questionValues->isNotEmpty()) {
                                                        $humanReadableAnswers = [];

                                                        $answer = is_array($answer) ? $answer : [$answer];

                                                        foreach ($answer as $subAnswer) {
                                                            $questionValue = $questionValues->where('value', '=', $subAnswer)->first();

                                                            if (! empty($questionValue)) {
                                                                $answerToAppend = $questionValue['name'];

                                                                if (! empty($questionValue['extra']['icon'])) {
                                                                    $answerToAppend .= '<i class="ml-1 w-8 h-8 ' . $questionValue['extra']['icon'] . '"></i>';
                                                                }

                                                                $humanReadableAnswers[] = $answerToAppend;
                                                            }
                                                        }

                                                        if (! empty($humanReadableAnswers)) {
                                                            $humanReadableAnswer = implode(', ', $humanReadableAnswers);
                                                        }
                                                    } else {
                                                        // If there are no question values, then it's user input
                                                        $humanReadableAnswer = $answer;
                                                    }

                                                    // Format numbers
                                                    if ($toolQuestionToSummarize->toolQuestionType->short === 'text' && \App\Helpers\Str::arrContains($toolQuestionToSummarize->validation, 'numeric')) {
                                                        $isInteger = \App\Helpers\Str::arrContains($toolQuestionToSummarize->validation, 'integer');
                                                        $humanReadableAnswer = \App\Helpers\NumberFormatter::format($humanReadableAnswer, $isInteger ? 0 : 1);
                                                        if ($isInteger) {
                                                            $humanReadableAnswer = str_replace('.', '', $humanReadableAnswer);
                                                        }
                                                    } elseif($toolQuestionToSummarize->toolQuestionType->short === 'slider') {
                                                        $humanReadableAnswer = str_replace('.', '', \App\Helpers\NumberFormatter::format($humanReadableAnswer, 0));
                                                    }
                                                }
                                            @endphp

                                            {!! $humanReadableAnswer !!}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>
        @endcan
    @endforeach

    <div class="flex flex-row flex-wrap w-full">
        @foreach($toolQuestions as $toolQuestion)
            @php
                $disabled = ! $building->user->account->can('answer', $toolQuestion);
            @endphp
                @component('cooperation.frontend.layouts.components.form-group', [
                    'label' => $toolQuestion->name . (is_null($toolQuestion->forSpecificInputSource) ? '' : " ({$toolQuestion->forSpecificInputSource->name})"),
                    'class' => 'w-full sm:w-1/2 ' . ($loop->iteration % 2 === 0 ? 'sm:pl-3' : 'sm:pr-3'),
                    'withInputSource' => ! $disabled,
                    'id' => "filledInAnswers-{$toolQuestion->id}",
                    'inputName' => "filledInAnswers.{$toolQuestion->id}",
                ])
                    @slot('sourceSlot')
                        @include('cooperation.sub-step-templates.parts.source-slot-values', [
                            'values' => $filledInAnswersForAllInputSources[$toolQuestion->id],
                            'toolQuestion' => $toolQuestion,
                        ])
                    @endslot

                    @slot('modalBodySlot')
                        <p>
                            {!! $toolQuestion->help_text !!}
                        </p>
                    @endslot

                    @include("cooperation.tool-question-type-templates.{$toolQuestion->toolQuestionType->short}.show", [
                        'disabled' => $disabled,
                    ])
                @endcomponent
        @endforeach
    </div>
</div>