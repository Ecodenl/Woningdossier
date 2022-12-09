<div class="w-full divide-y-2 divide-blue-500 divide-opacity-20 space-y-{{$toolQuestions->count() > 1 ? 10 : 5}} ">

    @php
        // some necessary crap to display the toolQuestions in the right manor
        $topLeftPivot = $subStep->subSteppables->where('order', 0)->first();
        $topLeft = optional($topLeftPivot)->subSteppable;
        $topRightFirstPivot = $subStep->subSteppables->where('order', 1)->first();
        $topRightFirst = optional($topRightFirstPivot)->subSteppable;
        $topRightSecondPivot = $subStep->subSteppables->where('order', 2)->first();
        $topRightSecond = optional($topRightSecondPivot)->subSteppable;

        $bottomLeftPivot = $subStep->subSteppables->where('order', 3)->first();
        $bottomLeft = optional($bottomLeftPivot)->subSteppable;
    @endphp
    <div class="w-full flex flex-wrap">
        @if($topLeft instanceof \App\Models\ToolQuestion)
            @php
                $disabled = ! $building->user->account->can('answer', $topLeft);
            @endphp
            @component('cooperation.frontend.layouts.components.form-group', [
                'class' => 'form-group-heading w-full lg:w-1/2 lg:pr-3',
                'label' => $topLeft->name,
                'inputName' => "filledInAnswers.{$topLeft->short}",
            ])
                @slot('sourceSlot')
                    @include('cooperation.sub-step-templates.parts.source-slot-values', [
                        'values' => $filledInAnswersForAllInputSources[$topLeft->short],
                        'toolQuestion' => $topLeft,
                    ])
                @endslot

                @slot('modalBodySlot')
                    <p>
                        {!! $topLeft->help_text !!}
                    </p>
                @endslot

                @include("cooperation.tool-question-type-templates.{$topLeftPivot->toolQuestionType->short}.show", ['toolQuestion' => $topLeft])
            @endcomponent
        @endif

        <div class="w-full lg:w-1/2 lg:pl-3">
            @if($topRightFirst instanceof \App\Models\ToolQuestion)
                @php
                    $disabled = ! $building->user->account->can('answer', $topRightFirst);
                @endphp
                @component('cooperation.frontend.layouts.components.form-group', [
                    'class' => 'form-group-heading w-full',
                    'label' => $topRightFirst->name,
                    'inputName' => "filledInAnswers.{$topRightFirst->short}",
                ])
                    @slot('sourceSlot')
                        @include('cooperation.sub-step-templates.parts.source-slot-values', [
                            'values' => $filledInAnswersForAllInputSources[$topRightFirst->short],
                            'toolQuestion' => $topRightFirst,
                        ])
                    @endslot

                    @slot('modalBodySlot')
                        <p>
                            {!! $topRightFirst->help_text !!}
                        </p>
                    @endslot

                    @include("cooperation.tool-question-type-templates.{$topRightFirstPivot->toolQuestionType->short}.show", ['toolQuestion' => $topRightFirst])
                @endcomponent
            @endif
            @if($topRightSecond instanceof \App\Models\ToolQuestion)
                @php
                    $disabled = ! $building->user->account->can('answer', $topRightSecond);
                @endphp
                @component('cooperation.frontend.layouts.components.form-group', [
                    'class' => 'form-group-heading w-full',
                    'label' => $topRightSecond->name,
                    'inputName' => "filledInAnswers.{$topRightSecond->short}",
                ])
                    @slot('sourceSlot')
                        @include('cooperation.sub-step-templates.parts.source-slot-values', [
                            'values' => $filledInAnswersForAllInputSources[$topRightSecond->short],
                            'toolQuestion' => $topRightSecond,
                        ])
                    @endslot

                    @slot('modalBodySlot')
                        <p>
                            {!! $topRightSecond->help_text !!}
                        </p>
                    @endslot

                    @include("cooperation.tool-question-type-templates.{$topRightSecondPivot->toolQuestionType->short}.show", ['toolQuestion' => $topRightSecond])
                @endcomponent
            @endif
        </div>
    </div>
    @if($bottomLeft instanceof \App\Models\ToolQuestion)
        @php
            $disabled = ! $building->user->account->can('answer', $bottomLeft);
        @endphp
        <div class="w-full pt-5">
            @component('cooperation.frontend.layouts.components.form-group', [
                'class' => 'form-group-heading w-full',
                'label' => $bottomLeft->name,
                'inputName' => "filledInAnswers.{$bottomLeft->short}",
                'inputGroupClass' => 'w-1/2',
            ])
                @slot('sourceSlot')
                    @include('cooperation.sub-step-templates.parts.source-slot-values', [
                        'values' => $filledInAnswersForAllInputSources[$bottomLeft->short],
                        'toolQuestion' => $bottomLeft,
                    ])
                @endslot

                @slot('modalBodySlot')
                    <p>
                        {!! $bottomLeft->help_text !!}
                    </p>
                @endslot

                @include("cooperation.tool-question-type-templates.{$bottomLeftPivot->toolQuestionType->short}.show", ['toolQuestion' => $bottomLeft])
            @endcomponent
        </div>
    @endif
</div>
