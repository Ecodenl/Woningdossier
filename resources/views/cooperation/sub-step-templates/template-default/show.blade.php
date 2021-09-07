<div class="w-full divide-y-2 divide-blue-500 divide-opacity-20 space-y-{{$toolQuestions->count() > 1 ? 10 : 5}} ">
    @foreach($toolQuestions as $toolQuestion)
        @php
            $disabled = ! $building->user->account->can('answer', $toolQuestion);
        @endphp
        <div class="w-full @if($loop->iteration > 1) pt-10 @endif">
            @component('cooperation.frontend.layouts.components.form-group', [
                'class' => 'form-group-heading',
                // 'defaultInputSource' => 'resident',
                'label' => $toolQuestion->name . (is_null($toolQuestion->forSpecificInputSource) ? '' : " ({$toolQuestion->forSpecificInputSource->name})"),
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

                @include("cooperation.tool-question-type-templates.{$toolQuestion->toolQuestionType->short}.show", [
                    'disabled' => $disabled,
                ])
            @endcomponent
        </div>
    @endforeach
</div>
