<div class="row">
    <div class="col-sm-12">
        <div class="form-group add-space{{ $errors->has('questions.'.$question->id) ? ' has-error' : '' }}">
            <label for="">{{$question->name}} @if($question->isRequired()) * @endif</label>
            @component('cooperation.tool.components.input-group',
            ['inputType' => 'input', 'userInputValues' => $question->questionAnswers()->forMe()->get(), 'userInputColumn' => 'answer'])
                <input @if($question->isRequired()) required="required" @endif name="questions[{{$question->id}}]" data-input-value="{{$value}}" placeholder="{{$question->name}}" value="{{old('questions.'.$question->id, $question->getAnswerForCurrentInputSource())}}" type="text" class="form-control">
            @endcomponent
            @if ($errors->has('questions.'.$question->id))
                <span class="help-block">
                    <strong>{{ $errors->first('questions.'.$question->id) }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>
