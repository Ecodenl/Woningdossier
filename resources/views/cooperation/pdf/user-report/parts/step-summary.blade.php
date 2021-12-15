<div class="question-answer-section">
    <p class="lead">
        {{$summaryStep->name}}
    </p>

    <table class="full-width">
        <tbody>
            {{-- Loop all sub steps except for the current (summary) step --}}
            @foreach($subStepsToSummarize as $subStepToSummarize)
                @if(\App\Helpers\Conditions\ConditionEvaluator::init()->building($building)->inputSource($inputSource)
                        ->evaluate($subStepToSummarize->conditions ?? []))
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
                                $humanReadableAnswer = \App\Helpers\ToolQuestionHelper::getHumanReadableAnswer(
                                    $building,
                                    ($toolQuestionToSummarize->forSpecificInputSource ?? $inputSource),
                                    $toolQuestionToSummarize,
                                    false,
                                    ($answers[$toolQuestionToSummarize->short] ?? null)
                                );

                                if ($toolQuestionToSummarize->toolQuestionType->short === 'text'
                                    && \App\Helpers\Str::arrContains($toolQuestionToSummarize->validation, 'numeric')) {
                                    // Apparently feedback said, no formatting for this, so we revert...
                                    if (isset($answers[$toolQuestionToSummarize->short])) {
                                        $humanReadableAnswer = $answers[$toolQuestionToSummarize->short];
                                    }
                                }

                                // Handle replaceables
                                $toolQuestionToSummarize = \App\Helpers\ToolQuestionHelper::handleToolQuestionReplaceables(
                                    $building,
                                    ($toolQuestionToSummarize->forSpecificInputSource ?? $inputSource),
                                    $toolQuestionToSummarize,
                                );
                            @endphp

                            <tr class="h-20">
                                <td class="w-380">{{ $toolQuestionToSummarize->name }}</td>
                                <td>{!! $humanReadableAnswer !!}</td>
                            </tr>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </tbody>
    </table>
</div>

@if(isset($commentsByStep[$summaryStep->short]['-']))
    @include('cooperation.pdf.user-report.parts.measure-page.comments', [
        'title' => __('pdf/user-report.general-data.comment'),
        'comments' => $commentsByStep[$summaryStep->short]['-'],
    ])
@else
    @foreach($commentsByStep[$summaryStep->short] as $stepCommentShort => $stepComment)
        @php
            // this is some custom code to retrieve the correct translation for the given comment.
            $toolQuestionShort = "residential-status-{$stepCommentShort}-comment-service";
            if (isset($stepComment["Bewoner"])) {
                $toolQuestionShort = "residential-status-{$stepCommentShort}-comment-resident";
            }
            $toolQuestion = App\Models\ToolQuestion::findByShort($toolQuestionShort);
        @endphp
        @include('cooperation.pdf.user-report.parts.measure-page.comments', [
              'title' => $toolQuestion->name,
            'comments' => $stepComment,
       ])
    @endforeach
@endif