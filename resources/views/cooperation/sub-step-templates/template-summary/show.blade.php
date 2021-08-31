<div class="w-full divide-y-2 divide-blue-500 divide-opacity-20 space-y-5">
    <div class="w-full">
        <h2 class="heading-2">
            {{ $subStep->name }}
        </h2>
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
            <div class="flex flex-row flex-wrap w-full space-y-4">
                <a href="{{ $subStepRoute }}"
                   class="no-underline my-4">
                    <div class="w-full">
                        <h3 class="heading-3 text-purple">
                            {{ $subStepToSummarize->name }}
                        </h3>
                    </div>
                </a>

                @php
                    $answers = [];
                @endphp
                {{-- We loop twice to first get all answers. We need the answers to ensure whether or not the tool question should be shown --}}
                @foreach($subStepToSummarize->toolQuestions as $toolQuestionToSummarize)
                    @php
                        // Answers will contain an array of arrays of all answers for the tool question in this sub step,
                        // in which the nested array will be short => answer based
                        $answers[$toolQuestionToSummarize->short] = $building->getAnswer($masterInputSource, $toolQuestionToSummarize);
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
                    @endphp

                    @if ($showQuestion)
                        <div class="flex flex-row flex-wrap w-full">
                            <div class="@if($toolQuestionToSummarize->toolQuestionType->short === 'rating-slider') w-full pb-2 @else w-1/2 @endif">
                                <a href="{{ $subStepRoute }}" class="no-underline">
                                    <h4 class="heading-4">
                                        - {{ $toolQuestionToSummarize->name }}
                                    </h4>
                                </a>
                            </div>

                            @if($toolQuestionToSummarize->toolQuestionType->short === 'rating-slider')
                                @foreach($toolQuestionToSummarize->options as $option)
                                    <div class="w-1/2">
                                        <h5 class="heading-5">
                                            - {{ $option['name'] }}
                                        </h5>
                                    </div>
                                    <div class="w-1/2 text-right">
                                        <p class="font-semibold">
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
                                <div class="w-1/2 text-right">
                                    <p class="font-semibold">
                                        @php
                                            $humanReadableAnswer = __('cooperation/frontend/tool.no-answer-given');
                                            $answer = $answers[$toolQuestionToSummarize->short] ?? null;

                                            if (! empty($answer)) {
                                                $questionValues = $toolQuestionToSummarize->getQuestionValues();

                                                if ($questionValues->isNotEmpty()) {
                                                    $humanReadableAnswers = [];

                                                    $answer = is_array($answer) ? $answer : [$answer];

                                                    foreach ($answer as $subAnswer) {
                                                        $questionValue = $questionValues->where('value', '=', $subAnswer)->first();

                                                        if (! empty($questionValue)) {
                                                            $humanReadableAnswers[] = $questionValue['name'];
                                                        }
                                                    }

                                                    if (! empty($humanReadableAnswers)) {
                                                        $humanReadableAnswer = implode(', ', $humanReadableAnswers);
                                                    }
                                                } else {
                                                    // If there are no question values, then it's user input
                                                    $humanReadableAnswer = $answer;
                                                }
                                            }
                                        @endphp

                                        {{ $humanReadableAnswer }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        @endcan
    @endforeach

</div>