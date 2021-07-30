<div class="input-group">
    <input class="form-input" id="{{$toolQuestion->short}}" wire:model="filledInAnswers.{{$toolQuestion['id']}}" placeholder="{{$toolQuestion->placeholder}}">
    @if(!empty($toolQuestion->unit_of_measure))
    <div class="input-group-append">
        {{$toolQuestion->unit_of_measure}}
    </div>
    @endif
</div>

