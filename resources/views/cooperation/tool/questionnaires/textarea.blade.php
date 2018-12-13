<div class="row">
    <div class="col-sm-12">
        <div class="form-group add-space{{ $errors->has('questions.'.$question->id) ? ' has-error' : '' }}">
            <label for="">{{$question->name}} @if($question->isRequired()) * @endif</label>
            <textarea name="questions[{{$question->id}}]" class="form-control" rows="5">{{old('questions.'.$question->id, $question->getAnswerForCurrentInputSource())}}</textarea>
        </div>
    </div>
</div>
