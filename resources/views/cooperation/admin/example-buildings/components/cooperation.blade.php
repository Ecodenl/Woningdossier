<div class="form-group {{ $errors->has('cooperation_id') ? ' has-error' : '' }}">
    <label for="cooperation">Cooperation:</label>
    <select id="cooperation" name="cooperation_id" class="form-control">
        <option value="" @if(!isset($exampleBuilding) || !$exampleBuilding->cooperation instanceof \App\Models\Cooperation)selected="selected"@endif>-</option>
        @foreach($cooperations as $cooperation)
            <option value="{{ $cooperation->id }}" @if(isset($exampleBuilding) && $exampleBuilding->cooperation instanceof \App\Models\Cooperation && $exampleBuilding->cooperation->id == $cooperation->id)selected="selected"@endif>{{ $cooperation->name }}</option>
        @endforeach
    </select>

    @if ($errors->has('cooperation_id'))
        <span class="help-block">
            <strong>{{ $errors->first('cooperation_id') }}</strong>
        </span>
    @endif
</div>