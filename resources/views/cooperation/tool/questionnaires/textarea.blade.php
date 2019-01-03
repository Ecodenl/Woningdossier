<div class="row">
    <div class="col-sm-12">
        <div class="form-group add-space{{ $errors->has('questions.'.$question->id) ? ' has-error' : '' }}">
            <label for="">{{$question->name}} @if($question->isRequired()) * @endif</label>
            <textarea @if($question->isRequired()) required="required" @endif name="questions[{{$question->id}}]" class="form-control" rows="5">{{old('questions.'.$question->id, $question->getAnswerForCurrentInputSource())}}</textarea>

            @if ($errors->has('questions.'.$question->id))
                <span class="help-block">
                    <strong>{{ $errors->first('questions.'.$question->id) }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>
