<div class="row">
    <div class="col-sm-12">
        <div class="form-group add-space{{ $errors->has('question.'.$question->id) ? ' has-error' : '' }}">
            <label for="">{{$question->name}} @if($question->isRequired()) * @endif</label>
            <input name="question[{{$question->id}}]" placeholder="{{$question->name}}" type="text" class="form-control">
        </div>
    </div>
</div>
