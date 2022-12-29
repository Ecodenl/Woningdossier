<div class="form-group @error(($input_name ?? '')) has-error @enderror">
    {{$slot}}

    @error($input_name ?? '')
        <span class="help-block">
            <strong>{{$message}}</strong>
        </span>
    @enderror
</div>