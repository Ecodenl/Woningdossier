{{-- Used in cooperation/frontend/layouts/parts/custom-questionnaire.blade.php --}}
<div class="flex flex-row flex-wrap w-full">
    <div class="w-full">
        @component('cooperation.frontend.layouts.components.form-group', [
            'inputName' => "questions.{$question->id}",
            'label' => $question->name,
            'id' => "questions-{$question->id}",
            'class' => ($question->isRequired() ? 'required' : ''),
        ])
            <textarea id="questions-{{$question->id}}" data-input-value="{{$question->id}}"
                      @if($question->isRequired()) required="required" @endif name="questions[{{$question->id}}]"
                      class="form-input"
            >{{old('questions.'.$question->id, \App\Helpers\Hoomdossier::getMostCredibleValueFromCollection($question->questionAnswers, 'answer'))}}</textarea>

        @endcomponent
{{--            @component('cooperation.tool.questionnaires.components.input-group',--}}
{{--            ['inputType' => 'input', 'userInputValues' => $question->questionAnswersForMe,'userInputColumn' => 'answer'])--}}
    </div>
</div>
