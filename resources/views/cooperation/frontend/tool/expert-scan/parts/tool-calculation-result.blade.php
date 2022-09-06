@php
    $calculationResult = $subSteppablePivot->subSteppable;
@endphp
<div class="{{$subSteppablePivot->size}}" wire:key="calculation-result-{{$calculationResult->id}}">
    @component('cooperation.frontend.layouts.components.form-group', [
        'class' => 'form-group-heading',
        'labelClass' => 'text-sm',
        // so we give the option to replace something in the question title
        'label' => $calculationResult->name,
        'inputName' => "calculationResults.{$calculationResult->short}",
        'withInputSource' => false,
    ])
        @slot('modalBodySlot')
            <p>
                {!! $calculationResult->help_text !!}
            </p>
        @endslot
    
        @if(! empty($calculationResult->unit_of_measure))
            <div class="input-group-prepend">
                {!! $calculationResult->unit_of_measure !!}
            </div>
        @endif        
        <input class="form-input"
               id="{{$calculationResult->short}}"
               wire:model="filledInAnswers.{{$calculationResult['id']}}"
               placeholder="{{$calculationResult->placeholder}}" type="text"
               disabled>
    @endcomponent
</div>