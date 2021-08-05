{{-- Used in cooperation/frontend/layouts/parts/custom-questionnaire.blade.php --}}
<div class="flex flex-row flex-wrap w-full">
    <div class="w-full">
        @component('cooperation.frontend.layouts.components.form-group', [
            'inputName' => "questions.{$question->id}",
            'label' => $question->name,
            'id' => "questions-{$question->id}",
            'class' => ($question->isRequired() ? 'required' : ''),
        ])
            <?php
            // explode it on pipe | and create a collection from it.
            $answers = collect(explode('|', \App\Helpers\Hoomdossier::getMostCredibleValueFromCollection($question->questionAnswers, 'answer')));
            ?>
            @foreach($question->questionOptions as $option)
                <div class="checkbox-wrapper pr-3">
                    <input type="checkbox" id="questions-option-{{$question->id}}" name="questions[{{$question->id}}][]"
                           value="{{$option->id}}"
                           @if(old('questions.'.$question->id) == $option->id || $answers->contains($option->id)) checked @endif>
                    <label for="questions-option-{{$question->id}}">
                        <span class="checkmark"></span>
                        <span>{{$option->name}}</span>
                    </label>
                </div>
            @endforeach
        @endcomponent

{{--            @component('cooperation.tool.questionnaires.components.input-group',--}}
{{--            ['inputType' => 'checkbox', 'inputValues' => $question->questionOptions, 'userInputValues' => $question->questionAnswersForMe])--}}
    </div>
</div>
