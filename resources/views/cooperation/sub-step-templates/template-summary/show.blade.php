<div class="w-full space-y-2">
    <div class="w-full mb-4">
        <h4 class="heading-4">
            {{ $subStep->name }}
        </h4>
    </div>

    @php
        $subStepsToSummarize = $step->subSteps()
            ->where('id', '!=', $subStep->id)
            ->with('subSteppables')
            ->orderBy('order')
            ->get();

        $allConditions = $subStepsToSummarize->pluck('conditions')
            ->merge($subStepsToSummarize->pluck('subSteppables.*.conditions')->flatten(1))
            ->filter()
            ->flatten(1)
            ->all();

        $evaluator = \App\Helpers\Conditions\ConditionEvaluator::init()
            ->building($building)
            ->inputSource($masterInputSource);

        $answers = $evaluator->getToolAnswersForConditions($allConditions);
    @endphp

    {{-- Loop all sub steps except for the current (summary) step --}}
    @foreach($subStepsToSummarize as $subStepToSummarize)
        {{-- Only display sub steps that are valid to the user --}}
        @if($evaluator->evaluateCollection($subStepToSummarize->conditions ?? [], $answers))
            @php
                $subStepRoute = route('cooperation.frontend.tool.simple-scan.index', [
                    'cooperation' => $cooperation, 'scan' => $scan, 'step' => $step, 'subStep' => $subStepToSummarize
                ]);
            @endphp
            <div class="flex flex-row flex-wrap w-full space-y-2">
                @php
                    $completed = $building->completedSubSteps()->forInputSource($masterInputSource)->where('sub_step_id', $subStepToSummarize->id)->exists();
                @endphp
                {{-- Custom changes has no tool questions, it's basically a whole other story --}}
                @if($subStepToSummarize->subStepTemplate->short === 'template-custom-changes')
                    <div class="flex flex-row flex-wrap w-full">
                        <div class="w-1/2">
                            <a href="{{ $subStepRoute }}" class="no-underline">
                                <h6 class="as-text font-bold @if(! $completed) text-orange @endif">
                                    @lang("livewire/cooperation/frontend/tool/simple-scan/custom-changes.question.{$scan->short}.label")
                                </h6>
                            </a>
                        </div>

                        <div class="w-1/2">
                            <p class="flex items-center">
                                @php
                                    $advisables = [];
                                    $type = \App\Helpers\Models\CooperationMeasureApplicationHelper::getTypeForScan($scan);
                                @endphp
                                @foreach($building->user->actionPlanAdvices()->forInputSource($masterInputSource)->get() as $advice)
                                    @php
                                        if ($advice->user_action_plan_advisable_type === \App\Models\CustomMeasureApplication::class) {
                                            $advisable = $advice->userActionPlanAdvisable()
                                                ->forInputSource($this->masterInputSource)
                                                ->first();
                                        } else {
                                            $advisable = $advice->userActionPlanAdvisable;
                                        }

                                        if ($advisable instanceof \App\Models\CustomMeasureApplication && $type === \App\Helpers\Models\CooperationMeasureApplicationHelper::SMALL_MEASURE) {
                                            $advisables[] = strip_tags($advisable->name);
                                        } elseif($advisable instanceof \App\Models\CooperationMeasureApplication) {
                                            $shouldBeExtensive = $type === \App\Helpers\Models\CooperationMeasureApplicationHelper::EXTENSIVE_MEASURE;

                                            if ($advisable->is_extensive_measure == $shouldBeExtensive) {
                                                $advisableToAppend = strip_tags($advisable->name);

                                                if (! empty($advisable->extra['icon'])) {
                                                    $advisableToAppend .= '<i class="ml-1 w-8 h-8 '. $advisable->extra['icon'] . '"></i>';
                                                }

                                                $advisables[] = $advisableToAppend;
                                            }
                                        }
                                    @endphp
                                @endforeach
                                {!! implode(', ', $advisables) !!}
                            </p>
                        </div>
                    </div>
                @else
                    @foreach($subStepToSummarize->subSteppables->where('sub_steppable_type', \App\Models\ToolQuestion::class) as $subSteppablePivot)
                        {{-- Only display questions that are valid to the user --}}
                        @php
                            $toolQuestionToSummarize = $subSteppablePivot->subSteppable;

                            $showQuestion = true;

                            if (! empty($subSteppablePivot->conditions)) {
                                $showQuestion = $evaluator->evaluateCollection($subSteppablePivot->conditions, $answers);
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
                                    ($toolQuestionToSummarize->forSpecificInputSource ?? $masterInputSource),
                                    $toolQuestionToSummarize,
                                    true,
                                    ($answers[$toolQuestionToSummarize->short] ?? null)
                                );

                                // Handle replaceables
                                $toolQuestionToSummarize = \App\Helpers\ToolQuestionHelper::handleToolQuestionReplaceables(
                                    $building,
                                    ($toolQuestionToSummarize->forSpecificInputSource ?? $masterInputSource),
                                    $toolQuestionToSummarize,
                                );
                            @endphp

                            <div class="flex flex-row flex-wrap w-full">
                                <div class="@if($subSteppablePivot->toolQuestionType->short === 'rating-slider') w-full @else w-1/2 @endif">
                                    <a href="{{ $subStepRoute }}" class="no-underline">
                                        <h6 class="as-text font-bold @if(! $completed) text-orange @endif">
                                            {{ $toolQuestionToSummarize->name }}
                                        </h6>
                                    </a>
                                </div>

                                @if(is_array($humanReadableAnswer))
                                    @foreach($humanReadableAnswer as $name => $answer)
                                        <div class="w-1/2 pl-2">
                                            <a href="{{ $subStepRoute }}" class="no-underline">
                                                <h6 class="as-text font-bold @if(! $completed) text-orange @endif">
                                                    {{ $name }}
                                                </h6>
                                            </a>
                                        </div>
                                        <div class="w-1/2">
                                            <p class="flex items-center">
                                                {{ $answer }}
                                            </p>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="w-1/2">
                                        <p class="flex items-center">
                                            {!! $humanReadableAnswer !!}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>
        @endif
    @endforeach

    <div class="flex flex-row flex-wrap w-full">
        @foreach($this->substeppables as $subSteppablePivot)
            @php
                $toolQuestion = $subSteppablePivot->subSteppable;
                $disabled = ! $building->user->account->can('answer', $toolQuestion);
            @endphp
            @component('cooperation.frontend.layouts.components.form-group', [
                'label' => $toolQuestion->name . (is_null($toolQuestion->forSpecificInputSource) ? '' : " ({$toolQuestion->forSpecificInputSource->name})"),
                'class' => 'w-full sm:w-1/2 ' . ($loop->iteration % 2 === 0 ? 'sm:pl-3' : 'sm:pr-3'),
                'withInputSource' => ! $disabled,
                'id' => "filledInAnswers-{$toolQuestion->short}",
                'inputName' => "filledInAnswers.{$toolQuestion->short}",
            ])
                @slot('sourceSlot')
                    @include('cooperation.sub-step-templates.parts.source-slot-values', [
                        'values' => $filledInAnswersForAllInputSources[$toolQuestion->short],
                        'toolQuestion' => $toolQuestion,
                    ])
                @endslot

                @slot('modalBodySlot')
                    <p>
                        {!! $toolQuestion->help_text !!}
                    </p>
                @endslot


                @include("cooperation.tool-question-type-templates.{$subSteppablePivot->toolQuestionType->short}.show", [
                    'disabled' => $disabled,
                ])

            @endcomponent
        @endforeach
    </div>
</div>