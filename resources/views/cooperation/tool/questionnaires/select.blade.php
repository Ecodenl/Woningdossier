<div class="row">
    <div class="col-sm-12">
        <div class="form-group add-space{{ $errors->has('questions.'.$question->id) ? ' has-error' : '' }}">
            <label for="">{{$question->name}} @if($question->isRequired()) * @endif</label>
            @component('cooperation.tool.questionnaires.components.input-group',
            ['inputType' => 'select', 'inputValues' => $question->questionOptions, 'userInputValues' => $question->questionAnswers()->forMe()->get(), 'userInputColumn' => 'answer'])
                <select name="questions[{{$question->id}}]" class="form-control">
                    @foreach($question->questionOptions as $option)
                        <option @if(old('questions.'.$question->id) == $option->id || $question->getAnswerForCurrentInputSource() == $option->id) selected data-input-value="{{$option->id}}" @endif value="{{$option->id}}">{{$option->name}}</option>
                    @endforeach
                </select>
            @endcomponent
            @if ($errors->has('questions.'.$question->id))
                <span class="help-block">
                    <strong>{{ $errors->first('questions.'.$question->id) }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>
