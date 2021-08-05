{{-- Used in cooperation/frontend/layouts/parts/custom-questionnaire.blade.php --}}
<div class="flex flex-row flex-wrap w-full">
    <div class="w-full">
        @component('cooperation.frontend.layouts.components.form-group', [
            'inputName' => "questions.{$question->id}",
            'label' => $question->name,
            'id' => "questions-{$question->id}",
            'class' => ($question->isRequired() ? 'required' : ''),
        ])
            @foreach($question->questionOptions as $option)
                <div class="radio-wrapper pr-3">
                    <input type="radio" id="questions-option-{{$question->id}}" name="questions[{{$question->id}}]"
                           value="{{$option->id}}"
                           @if(old('questions.'.$question->id, \App\Helpers\Hoomdossier::getMostCredibleValueFromCollection($question->questionAnswers, 'answer')) == $option->id) checked @endif>
                    <label for="questions-option-{{$question->id}}">
                        <span class="checkmark"></span>
                        <span>{{$option->name}}</span>
                    </label>
                </div>
            @endforeach
        @endcomponent

{{--            @component('cooperation.tool.questionnaires.components.input-group',--}}
{{--            ['inputType' => 'radio', 'inputValues' => $question->questionOptions, 'userInputValues' => $question->questionAnswersForMe, 'userInputColumn' => 'id'])--}}
        </div>
    </div>
</div>
