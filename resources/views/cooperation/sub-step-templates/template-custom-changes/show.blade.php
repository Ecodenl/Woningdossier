<div class="w-full divide-y-2 divide-blue-500 divide-opacity-20 space-y-{{$toolQuestions->count() > 1 ? 10 : 5}} ">
    @foreach($toolQuestions as $toolQuestion)
        <div class="w-full @if($loop->iteration > 1) pt-10 @endif">
            @component('cooperation.frontend.layouts.components.form-group', [
                'class' => 'form-group-heading',
                'label' => $toolQuestion->name,
                'inputName' => "filledInAnswers.{$toolQuestion->id}",
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

            @livewire('cooperation.frontend.tool.quick-scan.custom-changes')
        @endcomponent
    </div>
    @endforeach
</div>
