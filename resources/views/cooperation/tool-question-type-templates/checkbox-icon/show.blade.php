<div class="w-full grid grid-rows-1 grid-cols-4 grid-flow-row gap-4">
    @foreach($toolQuestion->getQuestionValues() as $toolQuestionValue)
        @php
            $id = $toolQuestionValue['short'] ?? $toolQuestionValue['calculate_value'] ?? $toolQuestionValue['value'];
        @endphp
        <div class="radio-wrapper media-wrapper">
            <input type="radio"
                   id="{{$id}}"
                   wire:model="filledInAnswers.{{$toolQuestion['id']}}"
                   value="{{$toolQuestionValue['value']}}"
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
<div class="checkbox-wrapper media-wrapper">
    <input type="checkbox" id="changes-dormer" name="changes" value="dormer">
    <label for="changes-dormer">
                            <span class="media-icon-wrapper">
                                <i class="icon-dormer"></i>
                            </span>
        <span class="checkmark"></span>
        <span>Dakkapel</span>
    </label>
</div>