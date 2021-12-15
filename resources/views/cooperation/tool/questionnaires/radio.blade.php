{{-- Used in cooperation/frontend/layouts/parts/custom-questionnaire.blade.php --}}
<div class="flex flex-row flex-wrap w-full">
    <div class="w-full">
        @component('cooperation.frontend.layouts.components.form-group', [
            'inputName' => "questions.{$question->id}",
            'label' => $question->name,
            'id' => "questions-{$question->id}",
            'class' => ($question->isRequired() ? 'required' : ''),
        ])
            @slot('sourceSlot')
                @include('cooperation.tool.questionnaires.components.questionnaire-source-list', [
                    'inputType' => 'radio', 'inputValues' => $question->questionOptions,
                    'userInputValues' => $question->questionAnswersForMe, 'userInputColumn' => 'id'
                ])
            @endslot
            @foreach($question->questionOptions as $option)
                <div class="radio-wrapper pr-3">
                    <input type="radio" id="questions-{{$question->id}}-option-{{$option->id}}" name="questions[{{$question->id}}]"
                           value="{{$option->id}}"
                           @if(old('questions.'.$question->id, \App\Helpers\Hoomdossier::getMostCredibleValueFromCollection($question->questionAnswers, 'answer')) == $option->id) checked @endif>
                    <label for="questions-{{$question->id}}-option-{{$option->id}}">
                        <span class="checkmark"></span>
                        <span>{{$option->name}}</span>
                    </label>
                </div>
            @endforeach
        @endcomponent
    </div>
</div>