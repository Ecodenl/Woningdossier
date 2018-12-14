<div class="row">
    <div class="col-sm-12">
        <div class="form-group add-space{{ $errors->has('questions.'.$question->id) ? ' has-error' : '' }}">
            <label for="">{{$question->name}} @if($question->isRequired()) * @endif</label>
            <br>
            @foreach($question->questionOptions as $option)
                <label class="radio-inline">
                    <input type="radio" name="questions[{{$question->id}}]" value="{{$option->id}}" @if(old('questions.'.$question->id, $question->getAnswerForCurrentInputSource()) == $option->id) checked @endif>
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
