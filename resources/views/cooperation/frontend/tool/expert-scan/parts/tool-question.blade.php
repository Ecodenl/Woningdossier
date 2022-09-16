@php
    $toolQuestion = $subSteppablePivot->subSteppable;
    $disabled = ! $building->user->account->can('answer', $toolQuestion);
    $humanReadableAnswer = null;
@endphp

<div class="{{$subSteppablePivot->size}}" wire:key="question-{{$toolQuestion->id}}">
    @component('cooperation.frontend.layouts.components.form-group', [
        'class' => 'form-group-heading',
        'labelClass' => 'text-sm',
        // 'defaultInputSource' => 'resident',
        // so we give the option to replace something in the question title
        'label' => __($toolQuestion->name . (is_null($toolQuestion->forSpecificInputSource) ? '' : " ({$toolQuestion->forSpecificInputSource->name})"), ['name' => $humanReadableAnswer]),
        'inputName' => "filledInAnswers.{$toolQuestion->id}",
        'withInputSource' => ! $disabled,
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
        @include("cooperation.tool-question-type-templates.{$subSteppablePivot->toolQuestionType->short}.show", [
            'disabled' => $disabled,
        ])
    @endcomponent
</div>