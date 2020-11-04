<div class="row">
    <div class="col-sm-12">
        <div class="form-group {{ $errors->has('questions.'.$question->id) ? ' has-error' : '' }}">
            <label for="">{{$question->name}} @if($question->isRequired()) * @endif</label>
            <br>
            <?php
                // explode it on pipe | and create a collection from it.
                $answers = collect(explode('|', \App\Helpers\Hoomdossier::getMostCredibleValueFromCollection($question->questionAnswers, 'answer')));
            ?>
            @component('cooperation.tool.questionnaires.components.input-group',
            ['inputType' => 'checkbox', 'inputValues' => $question->questionOptions, 'userInputValues' => $question->questionAnswersForMe])
                @foreach($question->questionOptions as $option)
                    <label class="checkbox-inline">
                        <input type="checkbox" name="questions[{{$question->id}}][]" value="{{$option->id}}" @if(old('questions.'.$question->id) == $option->id || $answers->contains($option->id)) checked @endif>
                        {{$option->name}}
                    </label>
                @endforeach
            @endcomponent
            @if ($errors->has('questions.'.$question->id))
                <span class="help-block">
                    <strong>{{ $errors->first('questions.'.$question->id) }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>
