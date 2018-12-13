<div class="row">
    <div class="col-sm-12">
        <div class="form-group add-space{{ $errors->has('questions.'.$question->id) ? ' has-error' : '' }}">
            <label for="">{{$question->name}} @if($question->isRequired()) * @endif</label>
            <br>
            @foreach($question->questionOptions as $option)
                <label class="checkbox-inline">
                    @foreach($question->getAnswerForCurrentInputSource() as $answerForQuestion)
                    <input type="checkbox" name="questions[{{$question->id}}][]" value="{{$option->id}}" @if(old('questions.'.$question->id, $answerForQuestion->answer) == $option->id) checked @endif>
                    @endforeach
                    {{$option->name}}
                </label>
            @endforeach
            @if ($errors->has('questions.'.$question->id))
                <span class="help-block">
                    <strong>{{ $errors->first('questions.'.$question->id) }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>
