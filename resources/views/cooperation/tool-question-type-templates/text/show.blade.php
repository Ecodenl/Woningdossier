<input class="form-input @if(!empty($toolQuestion->unit_of_measure)) with-append @endif"
       id="{{$toolQuestion->short}}" wire:model.lazy="filledInAnswers.{{$toolQuestion['short']}}"
       placeholder="{{$toolQuestion->placeholder}}" type="text"
       @if(($disabled ?? false))
           disabled
       @else
           x-on:input-updated.window="$el.setAttribute('disabled', true);"
           x-on:input-update-processed.window="$el.removeAttribute('disabled');"
       @endif>
@if(!empty($toolQuestion->unit_of_measure))
    <div class="input-group-append">
        {{$toolQuestion->unit_of_measure}}
    </div>
@endif
