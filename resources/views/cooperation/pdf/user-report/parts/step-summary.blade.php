<div class="question-answer-section">
    <p class="lead">
        {{$summaryStep->name}}
    </p>

    <table class="full-width">
        <tbody>
        {{-- Loop all sub steps except for the current (summary) step --}}
        @foreach($subStepsToSummarize as $subStepToSummarize)
            {{-- We loop twice to first get all answers. We need the answers to ensure whether or not the tool question should be shown --}}
            @foreach($subStepToSummarize->toolQuestions as $toolQuestionToSummarize)
                @php
                    // Answers will contain an array of arrays of all answers for the tool question in this sub step,
                    // in which the nested array will be short => answer based
                    $answers[$toolQuestionToSummarize->short] = $building->getAnswer(($toolQuestionToSummarize->forSpecificInputSource ?? $inputSource), $toolQuestionToSummarize);
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


                    <tr class="h-20">
                        <td class="w-380">{{ $toolQuestionToSummarize->name }}</td>
                        <td>{!! $humanReadableAnswer !!}</td>
                    </tr>
                @endif
            @endforeach
        @endforeach
        </tbody>
    </table>
</div>

@if(isset($commentsByStep[$summaryStep->short]['-']))
    @include('cooperation.pdf.user-report.parts.measure-page.comments', [
        'title' => __('pdf/user-report.general-data.comment'),
        'comments' => $commentsByStep[$summaryStep->short]['-'],
    ])
@endif