<div class="w-full grid grid-rows-1 grid-cols-4 grid-flow-row gap-4">
    @foreach($toolQuestion->getQuestionValues() as $toolQuestionValue)
        @php
            $id = $toolQuestionValue['short'] ?? $toolQuestionValue['calculate_value'] ?? $toolQuestionValue['value'];
        @endphp
        <div class="checkbox-wrapper media-wrapper">
            <input type="checkbox" wire:model="filledInAnswers.{{$toolQuestion['id']}}" id="{{$id}}" name="changes" value="{{$toolQuestionValue['value']}}">
            <label for="changes-dormer">
                            <span class="media-icon-wrapper">
                                <i class="{{$toolQuestionValue['extra']['icon'] ?? ''}}"></i>
                            </span>
                <span class="checkmark"></span>
                <span>{{$toolQuestionValue['name']}}</span>
            </label>
        </div>
    @endforeach
</div>
