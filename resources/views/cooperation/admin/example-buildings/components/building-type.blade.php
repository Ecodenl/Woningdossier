<div class="form-group {{ $errors->has('building_type_id') ? ' has-error' : '' }}">
    <?php
        $selected = old('building_type_id');
        if (is_null($selected)){
        	if (isset($exampleBuilding) && $exampleBuilding->buildingType instanceof \App\Models\BuildingType){
        		$selected = $exampleBuilding->buildingType->id;
            }
            else {
        		$selected = "";
            }
        }
    ?>

    <label for="building_type_id">@lang('woningdossier.cooperation.tool.general-data.building-type.title')</label>
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