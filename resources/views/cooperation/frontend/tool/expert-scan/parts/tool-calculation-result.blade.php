<div class="{{$subSteppablePivot->size}}" wire:key="question-{{$subSteppablePivot->subSteppable->id}}">
    @component('cooperation.frontend.layouts.components.form-group', [
        'class' => 'form-group-heading',
        'labelClass' => 'text-sm',
        // 'defaultInputSource' => 'resident',
        // so we give the option to replace something in the question title
        'label' => $subSteppablePivot->subSteppable->name,
        'inputName' => "calculationResults.{$subSteppablePivot->subSteppable->short}",
        'withInputSource' => false,
    ])

        @slot('modalBodySlot')
            <p>
                {!! $subSteppablePivot->subSteppable->help_text !!}
            </p>
        @endslot

        <input class="form-input @if(!empty($subSteppablePivot->subSteppable->unit_of_measure)) with-append @endif"
               id="{{$subSteppablePivot->subSteppable->short}}"
               wire:model="filledInAnswers.{{$subSteppablePivot->subSteppable['id']}}"
               placeholder="{{$subSteppablePivot->subSteppable->placeholder}}" type="text"
               disabled>

    @endcomponent
</div>