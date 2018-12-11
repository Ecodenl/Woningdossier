<div class="row">
    <div class="col-sm-12">
        <div class="form-group add-space{{ $errors->has('question.'.$question->id) ? ' has-error' : '' }}">
            <label for="">{{$question->name}} @if($question->isRequired()) * @endif</label>
            <textarea name="question[{{$question->id}}]" class="form-control"></textarea>
        </div>
    </div>
</div>
