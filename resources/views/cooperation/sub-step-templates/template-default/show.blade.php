<div class="w-full divide-y-2 divide-blue-500 divide-opacity-20 space-y-{{$toolQuestions->count() > 1 ? 10 : 5}} ">
    @foreach($toolQuestions as $toolQuestion)

        <div class="w-full @if($loop->iteration > 1) pt-10 @endif">
            @component('cooperation.frontend.layouts.components.form-group', [
                'class' => 'form-group-heading',
                'label' => $toolQuestion->name,
                'inputName' => "filledInAnswers.{$toolQuestion->id}",
            ])
                @slot('sourceSlot')
                    @include('cooperation.sub-step-templates.parts.source-slot-values', ['values' => $filledInAnswersForAllInputSources[$toolQuestion->id]])
                @endslot

                @slot('modalBodySlot')
                    <p>
                        {!! $toolQuestion->help_text !!}
                    </p>
                @endslot

                @include("cooperation.tool-question-type-templates.{$toolQuestion->toolQuestionType->short}.show")
            @endcomponent
        </div>
    @endforeach
</div>
