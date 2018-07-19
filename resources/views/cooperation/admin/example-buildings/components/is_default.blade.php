<div class="form-group">
    <label for="is_default">Default value</label>
    <select name="is_default" id="is_default" class="form-control">
        @foreach([0 => 'No', 1 => 'Yes'] as $val => $string)
            <option value="{{ $val }}" @if(isset($exampleBuilding) && $exampleBuilding->is_default == $val)selected="selected"@endif>{{ $string }}</option>
        @endforeach
    </select>
</div>