<div class="form-group @error(($input_name ?? '')) has-error @enderror">
    {{$slot}}

    @error($input_name ?? '')
        <span class="help-block">
            <strong>{{$errors->first($input_name)}}</strong>
        </span>
    @enderror
</div>