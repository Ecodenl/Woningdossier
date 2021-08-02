@foreach($toolQuestions as $toolQuestion)
    <div class="w-full @if($loop->iteration > 1) pt-10 @endif">
        @component('cooperation.frontend.layouts.components.form-group', [
            'class' => 'form-group-heading',
            'label' => $toolQuestion->name,
        ])
            @slot('modalBodySlot')
                <p>
                    {!! $toolQuestion->help_text !!}
                </p>
            @endslot

        @endcomponent
    </div>
@endforeach
