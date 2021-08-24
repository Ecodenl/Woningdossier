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
                $subStepRoute = route('cooperation.frontend.tool.quick-scan.index', ['step' => $step, 'subStep' => $subStepToSummarize]);
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
                    $answers = collect([]);
                @endphp
                {{-- We loop twice to first get all answers. We need the answers to ensure whether or not the tool question should be shown --}}
                @foreach($subStepToSummarize->toolQuestions as $toolQuestionToSummarize)
                    @php
                        $answers->push([$toolQuestionToSummarize->short => $building->getAnswer($masterInputSource, $toolQuestionToSummarize)]);
                    @endphp
                @endforeach

                @foreach($subStepToSummarize->toolQuestions as $toolQuestionToSummarize)
                    {{-- Only display questions that are valid to the user --}}
                    @php
                        $showToolQuestion = true;

                        if (!empty($toolQuestionToSummarize->conditions)) {
                            foreach ($toolQuestionToSummarize->conditions as $condition) {
                                if ($answers->where($condition['column'], '!=', null)->count() > 0) {
                                    // now execute the actual condition
                                    $answer = $answers->where($condition['column'], $condition['operator'], $condition['value'])->first();
                                    // so this means the answer is not found, so we don't show the tool question
                                    if ($answer === null) {
                                        $showToolQuestion = false;
                                    }
                                }
                            }
                        }
                    @endphp
                    @if ($showToolQuestion)
                        <div class="flex flex-row flex-wrap w-full">
                            <div class="@if($toolQuestionToSummarize->toolQuestionType->short === 'rating-slider') w-1/2 pb-1 border-b border-blue-500 border-opacity-20 @else w-full @endif">
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
                                            {{ $option['name'] }}
                                        </h5>
                                    </div>
                                    <div class="w-1/2 text-right">
                                        <p>

                                        </p>
                                    </div>
                                @endforeach
                            @else
                                <div class="w-1/2 text-right">
                                    <p>
                                        {{ '$answer' }}
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