<div class="w-full divide-y-2 divide-blue-500 divide-opacity-20 space-y-{{$toolQuestions->count() > 1 ? 10 : 5}} ">

    @php
        // some necessary crap to display the toolQuestions in the right manor
        $topLeft = $toolQuestions->where('pivot.order', 0)->first();
        $topRightFirst = $toolQuestions->where('pivot.order', 1)->first();
        $topRightSecond = $toolQuestions->where('pivot.order', 2)->first();

        $bottomLeft = $toolQuestions->where('pivot.order', 3)->first();
    @endphp
    <div class="w-full flex flex-wrap">
        @if($topLeft instanceof \App\Models\ToolQuestion)
            @php
                $disabled = ! $building->user->account->can('answer', $topLeft);
            @endphp
            @component('cooperation.frontend.layouts.components.form-group', [
                'class' => 'form-group-heading w-full lg:w-1/2 lg:pr-3',
                'label' => $topLeft->name,
                'inputName' => "filledInAnswers.{$topLeft->id}",
             ])
                @slot('sourceSlot')
                    @include('cooperation.sub-step-templates.parts.source-slot-values', [
                        'values' => $filledInAnswersForAllInputSources[$topLeft->id],
                        'toolQuestion' => $topLeft,
                    ])
                @endslot

                @slot('modalBodySlot')
                    <p>
                        {!! $topLeft->help_text !!}
                    </p>
                @endslot

                @include("cooperation.tool-question-type-templates.{$topLeft->toolQuestionType->short}.show", ['toolQuestion' => $topLeft])
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
                    'inputName' => "filledInAnswers.{$topRightFirst->id}",
                 ])
                    @slot('sourceSlot')
                        @include('cooperation.sub-step-templates.parts.source-slot-values', [
                            'values' => $filledInAnswersForAllInputSources[$topRightFirst->id],
                            'toolQuestion' => $topRightFirst,
                        ])
                    @endslot

                    @slot('modalBodySlot')
                        <p>
                            {!! $topRightFirst->help_text !!}
                        </p>
                    @endslot

                    @include("cooperation.tool-question-type-templates.{$topRightFirst->toolQuestionType->short}.show", ['toolQuestion' => $topRightFirst])
                @endcomponent
            @endif
            @if($topRightSecond instanceof \App\Models\ToolQuestion)
                @php
                    $disabled = ! $building->user->account->can('answer', $topRightSecond);
                @endphp
                @component('cooperation.frontend.layouts.components.form-group', [
                    'class' => 'form-group-heading w-full',
                    'label' => $topRightSecond->name,
                    'inputName' => "filledInAnswers.{$topRightSecond->id}",
                ])
                    @slot('sourceSlot')
                        @include('cooperation.sub-step-templates.parts.source-slot-values', [
                            'values' => $filledInAnswersForAllInputSources[$topRightSecond->id],
                            'toolQuestion' => $topRightSecond,
                        ])
                    @endslot

                    @slot('modalBodySlot')
                        <p>
                            {!! $topRightSecond->help_text !!}
                        </p>
                    @endslot

                    @include("cooperation.tool-question-type-templates.{$topRightSecond->toolQuestionType->short}.show", ['toolQuestion' => $topRightSecond])
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
                'inputName' => "filledInAnswers.{$bottomLeft->id}",
                'inputGroupClass' => 'w-1/2',
            ])
                @slot('sourceSlot')
                    @include('cooperation.sub-step-templates.parts.source-slot-values', [
                        'values' => $filledInAnswersForAllInputSources[$bottomLeft->id],
                        'toolQuestion' => $bottomLeft,
                    ])
                @endslot

                @slot('modalBodySlot')
                    <p>
                        {!! $bottomLeft->help_text !!}
                    </p>
                @endslot

                @include("cooperation.tool-question-type-templates.{$bottomLeft->toolQuestionType->short}.show", ['toolQuestion' => $bottomLeft])
            @endcomponent
        </div>
    @endif
</div>
