<input class="form-input @if(!empty($toolQuestion->unit_of_measure)) with-append @endif"
       id="{{$toolQuestion->short}}" wire:model="filledInAnswers.{{$toolQuestion['id']}}"
       placeholder="{{$toolQuestion->placeholder}}" type="text" @if(($disabled ?? false)) disabled @endif>
@if(!empty($toolQuestion->unit_of_measure))
    <div class="input-group-append">
        {{$toolQuestion->unit_of_measure}}
    </div>
@endif
