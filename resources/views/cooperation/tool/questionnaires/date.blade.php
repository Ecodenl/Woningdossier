{{-- Used in cooperation/frontend/layouts/parts/custom-questionnaire.blade.php --}}
<?php
    $value = \App\Helpers\Hoomdossier::getMostCredibleValueFromCollection($question->questionAnswers, 'answer');
?>
<div class="flex flex-row flex-wrap w-full">
    <div class="w-full">
        @component('cooperation.frontend.layouts.components.form-group', [
            'inputName' => "questions.{$question->id}",
            'label' => $question->name,
            'id' => "questions-{$question->id}",
            'class' => ($question->isRequired() ? 'required' : ''),
        ])
            @slot('sourceSlot')
                @include('cooperation.tool.questionnaires.components.questionnaire-source-list')
            @endslot
            <input @if($question->isRequired()) required="required" @endif name="questions[{{$question->id}}]"
                   data-input-value="{{$question->id}}" placeholder="{{$question->name}}"
                   id="questions-{{$question->id}}" type="date" class="form-input"
                   value="{{old('questions.'.$question->id, $value)}}">
        @endcomponent
    </div>
</div>
