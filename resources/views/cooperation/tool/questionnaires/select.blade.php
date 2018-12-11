<div class="row">
    <div class="col-sm-12">
        <div class="form-group add-space{{ $errors->has('question.'.$question->id) ? ' has-error' : '' }}">
            <label for="">{{$question->name}} @if($question->isRequired()) * @endif</label>
            <select name="question[{{$question->id}}]" class="form-control">
                @foreach($question->questionOptions as $option)
                    <option value="{{$option->id}}">{{$option->name}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
