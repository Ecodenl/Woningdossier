<div class="w-full grid grid-rows-1 grid-cols-4 grid-flow-row gap-4">
    @foreach($toolQuestion->getQuestionValues() as $toolQuestionValue)
        @php
            $id = $toolQuestionValue['short'] ?? $toolQuestionValue['calculate_value'] ?? $toolQuestionValue['value'];
        @endphp
        <div class="checkbox-wrapper media-wrapper">
            <input id="{{$id}}" type="checkbox" wire:model="filledInAnswers.{{$toolQuestion['id']}}"  name="{{$id}}" value="{{$toolQuestionValue['value']}}"
                   @if($disabled) disabled="disabled" @endif
            >
            <label for="{{$id}}">
                            <span class="media-icon-wrapper">
                                <i class="{{$toolQuestionValue['extra']['icon'] ?? ''}}"></i>
                            </span>
                <span class="checkmark"></span>
                <span>{{$toolQuestionValue['name']}}</span>
            </label>
        </div>
    @endforeach
</div>
