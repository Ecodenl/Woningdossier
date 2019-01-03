<div class="row">
    <div class="col-sm-12">
        <div class="form-group add-space{{ $errors->has('questions.'.$question->id) ? ' has-error' : '' }}">
            <label for="">{{$question->name}} @if($question->isRequired()) * @endif</label>

            <input @if($question->isRequired()) required="required" @endif name="questions[{{$question->id}}]" placeholder="{{$question->name}}" value="{{old('questions.'.$question->id, $question->getAnswerForCurrentInputSource())}}" type="date" class="form-control">

            @if ($errors->has('questions.'.$question->id))
                <span class="help-block">
                    <strong>{{ $errors->first('questions.'.$question->id) }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>
