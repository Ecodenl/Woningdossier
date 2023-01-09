<div class="question-answer-section">
    <p class="lead">
        {{$summaryStep->name}}
    </p>

    @php
        $allConditions = $subStepsToSummarize->pluck('conditions')
            ->merge($subStepsToSummarize->pluck('subSteppables.*.conditions')->flatten(1))
            ->filter()
            ->flatten(1)
            ->all();

        $evaluator = \App\Helpers\Conditions\ConditionEvaluator::init()
            ->building($building)
            ->inputSource($inputSource);

        $answers = $evaluator->getToolAnswersForConditions($allConditions);
    @endphp

    <table class="full-width">
        <tbody>
        {{-- Loop all sub steps except for the current (summary) step --}}
        @foreach($subStepsToSummarize as $subStepToSummarize)
            @if($evaluator->evaluateCollection($subStepToSummarize->conditions ?? [], $answers))
                @foreach($subStepToSummarize->toolQuestions as $toolQuestionToSummarize)
                    {{-- Only display questions that are valid to the user --}}
                    @php
                        $showQuestion = true;

                        if (! empty($toolQuestionToSummarize->pivot->conditions)) {
                            $showQuestion = $evaluator->evaluateCollection($toolQuestionToSummarize->pivot->conditions, $answers);
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

                            if ($toolQuestionToSummarize->data_type === \App\Helpers\DataTypes\Caster::FLOAT && isset($answers[$toolQuestionToSummarize->short])) {
                                // Apparently feedback said, no formatting for this, so we revert...
                                $humanReadableAnswer = $answers[$toolQuestionToSummarize->short];
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
@elseif (isset($commentsByStep[$summaryStep->short]))
    @foreach($commentsByStep[$summaryStep->short] as $stepCommentShort => $stepComment)
        @php
            // this is some custom code to retrieve the correct translation for the given comment.
            $toolQuestionShort = "{$summaryStep->short}-{$stepCommentShort}-comment-coach";
            if (isset($stepComment["Bewoner"])) {
                $toolQuestionShort = "{$summaryStep->short}-{$stepCommentShort}-comment-resident";
            }
            $toolQuestion = App\Models\ToolQuestion::findByShort($toolQuestionShort);
        @endphp
        @if($toolQuestion instanceof \App\Models\ToolQuestion)

            @include('cooperation.pdf.user-report.parts.measure-page.comments', [
                'title' => $toolQuestion->name,
                'comments' => $stepComment,
            ])
        @endif
    @endforeach
@endif