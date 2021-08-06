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
                    'inputType' => 'select', 'inputValues' => $question->questionOptions,
                    'userInputValues' => $question->questionAnswersForMe, 'userInputColumn' => 'answer'
                ])
            @endslot
            @component('cooperation.frontend.layouts.components.alpine-select')
                <select id="questions-{{$question->id}}" name="questions[{{$question->id}}]" class="form-input">
                    @foreach($question->questionOptions as $option)
                        <option value="{{$option->id}}"
                                @if(old('questions.'.$question->id) == $option->id || \App\Helpers\Hoomdossier::getMostCredibleValueFromCollection($question->questionAnswers, 'answer') == $option->id) selected data-input-value="{{$option->id}}" @endif>
                            {{$option->name}}
                        </option>
                    @endforeach
                </select>
            @endcomponent
        @endcomponent
    </div>
</div>
