@extends('cooperation.tool.layout')

@section('step_title', \App\Helpers\Translation::translate('general-data.title.title'))

@section('step_content')
    <form class="form-horizontal" method="POST"
          action="{{ route('cooperation.tool.building-details.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-md-6">
                <div class="form-group add-space{{ $errors->has('building_type_id') ? ' has-error' : '' }}">
                    <label for="building_type_id" class=" control-label">
                        <i data-toggle="collapse" data-target="#building-type-info"
                           class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                           aria-expanded="false"></i>
                        {{\App\Helpers\Translation::translate('general-data.building-type.what-type.title')}}
                    </label>

                    @component('cooperation.tool.components.input-group', [
                        'inputType' => 'select',
                        'inputValues' => $buildingTypes,
                        'userInputValues' => $building->buildingFeatures()->forMe()->get(),
                        'userInputModel' => 'buildingType',
                        'userInputColumn' => 'building_type_id'
                    ])
                        <select id="building_type_id" class="form-control" name="building_type_id">
                            @foreach($buildingTypes as $buildingType)
                                <option @if(old('building_type_id') && $buildingType->id == old('building_type_id'))
                                        selected="selected"
                                        @elseif(isset($building->buildingFeatures->buildingType) && $building->buildingFeatures->buildingType->id == $buildingType->id)
                                        selected="selected"
                                        @endif value="{{ $buildingType->id }}">{{ $buildingType->name }}
                                </option>
                            @endforeach
                        </select>
                    @endcomponent

                    <div id="building-type-info"
                         class="collapse alert alert-info remove-collapse-space alert-top-space">
                        {{\App\Helpers\Translation::translate('general-data.building-type.what-type.help')}}
                    </div>
                    @if ($errors->has('building_type_id'))
                        <span class="help-block">
                                <strong>{{ $errors->first('building_type_id') }}</strong>
                            </span>
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group add-space{{ $errors->has('build_year') ? ' has-error' : '' }}">
                    <label for="build_year" class=" control-label">
                        <i data-toggle="collapse" data-target="#what-building-year-info"
                           class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                        {{\App\Helpers\Translation::translate('general-data.building-type.what-building-year.title')}}
                        <span>*</span>
                    </label>

                    @component('cooperation.tool.components.input-group',
                    ['inputType' => 'input', 'userInputValues' => $building->buildingFeatures()->forMe()->get(), 'userInputColumn' => 'build_year'])
                        <input id="build_year" type="text" class="form-control" name="build_year"
                               value="{{ old('build_year', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'build_year')) }}"
                               required autofocus>
                    @endcomponent

                    <div id="what-building-year-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                        {{\App\Helpers\Translation::translate('general-data.building-type.what-building-year.help')}}
                    </div>

                    @if ($errors->has('build_year'))
                        <span class="help-block">
                                <strong>{{ $errors->first('build_year') }}</strong>
                            </span>
                    @endif
                </div>
            </div>
        </div>
    </form>
@endsection