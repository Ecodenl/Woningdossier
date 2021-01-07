<div class="form-group {{ $errors->has('building_type_id') ? ' has-error' : '' }}">
    <?php
        $selected = old('building_type_id', isset($exampleBuilding) ? $exampleBuilding->building_type_id : null);
    ?>

    <label for="building_type_id">{{\App\Helpers\Translation::translate('building-detail.building-type.what-type.title')}}</label>
    <select id="building_type_id" name="building_type_id" class="form-control">
        <option value="" @if($selected == "")selected="selected"@endif>-</option>
        @foreach($buildingTypes as $buildingType)
            <option value="{{ $buildingType->id }}" @if($selected == $buildingType->id)selected="selected"@endif>{{ $buildingType->name }}</option>
        @endforeach
    </select>

    @if ($errors->has('building_type_id'))
        <span class="help-block">
            <strong>{{ $errors->first('building_type_id') }}</strong>
        </span>
    @endif
</div>