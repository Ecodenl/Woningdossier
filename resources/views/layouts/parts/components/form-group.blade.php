<div class="form-group {{ $errors->has($input_name) ? 'has-error' : '' }}">

    {{$slot}}

    @error($input_name)
        <span class="help-block">
            <strong>{{$message}}</strong>
        </span>
    @enderror
</div>