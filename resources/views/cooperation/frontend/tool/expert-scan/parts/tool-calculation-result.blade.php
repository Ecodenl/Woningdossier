@php
    $calculationResultField = $subSteppablePivot->subSteppable;
@endphp
<div class="{{$subSteppablePivot->size}}" wire:key="calculation-result-{{$calculationResultField->id}}">
    @component('cooperation.frontend.layouts.components.form-group', [
        'class' => 'form-group-heading',
        'labelClass' => 'text-sm',
        // so we give the option to replace something in the question title
        'label' => $calculationResultField->name,
        'inputName' => "calculationResults.{$calculationResultField->short}",
        'withInputSource' => false,
    ])
        @slot('modalBodySlot')
            <p>
                {!! $calculationResultField->help_text !!}
            </p>
        @endslot
    
        @if(! empty($calculationResultField->unit_of_measure))
            <div class="input-group-prepend">
                {!! $calculationResultField->unit_of_measure !!}
            </div>
        @endif        
        <input class="form-input" autocomplete="off"
               id="{{$calculationResultField->short}}"
               wire:model="calculationResults.{{$calculationResultField->short}}"
               placeholder="{{$calculationResultField->placeholder}}" type="text"
               disabled>
    @endcomponent
</div>