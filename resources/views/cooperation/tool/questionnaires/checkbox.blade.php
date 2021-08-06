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
                    'inputType' => 'checkbox', 'inputValues' => $question->questionOptions,
                    'userInputValues' => $question->questionAnswersForMe
                ])
            @endslot
            <?php
            // explode it on pipe | and create a collection from it.
            $answers = collect(explode('|', \App\Helpers\Hoomdossier::getMostCredibleValueFromCollection($question->questionAnswers, 'answer')));
            ?>
            @foreach($question->questionOptions as $option)
                <div class="checkbox-wrapper pr-3">
                    <input type="checkbox" id="questions-{{$question->id}}-option-{{$option->id}}" name="questions[{{$question->id}}][]"
                           value="{{$option->id}}"
                           @if(old('questions.'.$question->id) == $option->id || $answers->contains($option->id)) checked @endif>
                    <label for="questions-{{$question->id}}-option-{{$option->id}}">
                        <span class="checkmark"></span>
                        <span>{{$option->name}}</span>
                    </label>
                </div>
            @endforeach
        @endcomponent
    </div>
</div>
