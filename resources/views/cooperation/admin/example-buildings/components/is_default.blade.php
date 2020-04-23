<div class="form-group">
    <label for="is_default">@lang('cooperation/admin/example-buildings.components.is-default.label')</label>
    <select name="is_default" id="is_default" class="form-control">
        @foreach(__('cooperation/admin/example-buildings.components.is-default.options') as $val => $string)
            <option value="{{ $val }}" @if(isset($exampleBuilding) && $exampleBuilding->is_default == $val)selected="selected"@endif>{{ $string }}</option>
        @endforeach
    </select>
</div>