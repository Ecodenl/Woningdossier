<div class="w-full divide-y-2 divide-blue-500 divide-opacity-20 space-y-{{$this->subSteppables->count() > 1 ? 10 : 5}} ">

    @php
        // Required setup to display toolQuestions in the correct manner.
        $topPivot = $this->subSteppables->where('order', 0)->first();
        $top = $topPivot?->subSteppable;
        $bottomLeftPivot = $this->subSteppables->where('order', 1)->first();
        $bottomLeft = $bottomLeftPivot?->subSteppable;
        $bottomRightPivot = $this->subSteppables->where('order', 2)->first();
        $bottomRight = $bottomRightPivot?->subSteppable;
    @endphp
    <div class="w-full">
        @component('cooperation.frontend.layouts.components.form-group', [
            'class' => 'form-group-heading',
            'label' => $top->name,
            'inputName' => "filledInAnswers.{$top->short}",
            'id' => $top->short,
        ])
            @php
                $disabled = ! $building->user->account->can('answer', $top);
            @endphp
            @slot('sourceSlot')
                @include('cooperation.sub-step-templates.parts.source-slot-values', [
                    'values' => $filledInAnswersForAllInputSources[$top->short],
                    'toolQuestion' => $top,
                ])
            @endslot

            @slot('modalBodySlot')
                <p>
                    {!! $top->help_text !!}
                </p>
            @endslot

            @include("cooperation.tool-question-type-templates.{$topPivot->toolQuestionType->short}.show", ['toolQuestion' => $top])
        @endcomponent
    </div>
    <div class="pt-5 grid grid-cols-1 gap-x-6 sm:grid-cols-2">
        @if($bottomLeft instanceof \App\Models\ToolQuestion)
            @php
                $disabled = ! $building->user->account->can('answer', $bottomLeft);
            @endphp
            @component('cooperation.frontend.layouts.components.form-group', [
                'class' => 'form-group-heading w-full',
                'label' => $bottomLeft->name,
                'inputName' => "filledInAnswers.{$bottomLeft->short}",
                'id' => $bottomLeft->short,
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
        @endif

        @if($bottomRight instanceof \App\Models\ToolQuestion)
            @php
                $disabled = ! $building->user->account->can('answer', $bottomRight);
            @endphp
            @component('cooperation.frontend.layouts.components.form-group', [
                'class' => 'form-group-heading w-full ',
                'label' => $bottomRight->name,
                'inputName' => "filledInAnswers.{$bottomRight->short}",
                'id' => $bottomRight->short,
            ])
                @slot('sourceSlot')
                    @include('cooperation.sub-step-templates.parts.source-slot-values', [
                        'values' => $filledInAnswersForAllInputSources[$bottomRight->short],
                        'toolQuestion' => $bottomRight,
                    ])
                @endslot

                @slot('modalBodySlot')
                    <p>
                        {!! $bottomRight->help_text !!}
                    </p>
                @endslot

                @include("cooperation.tool-question-type-templates.{$bottomRightPivot->toolQuestionType->short}.show", ['toolQuestion' => $bottomRight])
            @endcomponent
        @endif
    </div>
</div>
