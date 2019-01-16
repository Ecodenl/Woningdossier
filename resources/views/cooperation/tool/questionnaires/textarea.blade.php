<div class="row">
    <div class="col-sm-12">
        <div class="form-group add-space{{ $errors->has('questions.'.$question->id) ? ' has-error' : '' }}">
            @component('cooperation.tool.components.input-group',
            ['inputType' => 'input', 'userInputValues' => $question->questionAnswers()->forMe()->get(),'userInputColumn' => 'answer'])
                <label for="">{{$question->name}} @if($question->isRequired()) * @endif</label>
                <textarea @if($question->isRequired()) required="required" @endif name="questions[{{$question->id}}]" data-input-value="{{$value}}" class="form-control" rows="5">{{old('questions.'.$question->id, $question->getAnswerForCurrentInputSource())}}</textarea>
            @endcomponent

            @if ($errors->has('questions.'.$question->id))
                <span class="help-block">
                    <strong>{{ $errors->first('questions.'.$question->id) }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>
