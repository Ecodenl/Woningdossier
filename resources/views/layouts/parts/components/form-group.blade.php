<div class="form-group{{ $errors->has($input_name) ? ' has-error' : '' }}">

    {{$slot}}

    @if(isset($input_name) && $errors->has($input_name))
        <span class="help-block">
            <strong>{{$errors->first($input_name)}}</strong>
        </span>
    @endif
</div>