@php
    $toolQuestion = $subSteppablePivot->subSteppable;

    $disabled = ! $building->user->account->can('answer', $toolQuestion);
    $humanReadableAnswer = null;

    switch ($toolQuestion->short) {
        case 'building-type':
            $rawAnswer = $building->getAnswer($masterInputSource, \App\Models\ToolQuestion::findByShort('building-type-category'));
            // if there is an answer we can find the row and get the answer.
            $model = \App\Models\BuildingTypeCategory::find($rawAnswer);
            if ($model instanceof \App\Models\BuildingTypeCategory) {
                $humanReadableAnswer = Str::lower($model->name);
            }
            break;
    }
@endphp

<div class="w-full @if($loop->iteration > 1) pt-10 @endif" wire:key="question-{{$toolQuestion->short}}">
    @component('cooperation.frontend.layouts.components.form-group', [
        'class' => 'form-group-heading',
        // 'defaultInputSource' => 'resident',
        // so we give the option to replace something in the question title
        'label' => __($toolQuestion->name . (is_null($toolQuestion->forSpecificInputSource) ? '' : " ({$toolQuestion->forSpecificInputSource->name})"), ['name' => $humanReadableAnswer]),
        'inputName' => "filledInAnswers.{$toolQuestion->short}",
        'withInputSource' => ! $disabled,
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
</div>