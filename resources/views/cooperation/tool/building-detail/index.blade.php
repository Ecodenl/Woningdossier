@extends('cooperation.tool.layout')

@section('step_title', \App\Helpers\Translation::translate('building-detail.title.title'))

@section('step_content')
    <form class="form-horizontal" method="POST"
          action="{{ route('cooperation.tool.building-detail.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-md-12">
                <p>{{ \App\Helpers\Translation::translate('building-detail.intro.title') }}</p>
            </div>
            <div class="col-md-6">
                <div class="form-group add-space{{ $errors->has('building_type_id') ? ' has-error' : '' }}">
                    <label for="building_type_id" class=" control-label">
                        <i data-toggle="modal" data-target="#building-type-info"
                           class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                           aria-expanded="false"></i>
                        {{\App\Helpers\Translation::translate('building-detail.building-type.what-type.title')}}
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
                                <option @if($buildingType->id == old('building_type_id', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'building_type_id')))
                                        selected="selected"
                                        @endif value="{{ $buildingType->id }}">{{ $buildingType->name }}
                                </option>
                            @endforeach
                        </select>
                    @endcomponent

                    <div id="building-type-info"
                         class="collapse alert alert-info remove-collapse-space alert-top-space">
                        {{\App\Helpers\Translation::translate('building-detail.building-type.what-type.help')}}
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
                        <i data-toggle="modal" data-target="#what-building-year-info"
                           class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                        {{\App\Helpers\Translation::translate('building-detail.building-type.what-building-year.title')}}
                        <span>*</span>
                    </label>

                    @component('cooperation.tool.components.input-group',
                    ['inputType' => 'input', 'userInputValues' => $building->buildingFeatures()->forMe()->get(), 'userInputColumn' => 'build_year'])
                        <input id="build_year" type="text" class="form-control" name="build_year"
                               value="{{ old('build_year', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'build_year')) }}"
                               required autofocus>
                    @endcomponent

                    <div id="what-building-year-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                        {{\App\Helpers\Translation::translate('building-detail.building-type.what-building-year.help')}}
                    </div>

                    @if ($errors->has('build_year'))
                        <span class="help-block">
                                <strong>{{ $errors->first('build_year') }}</strong>
                            </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <hr>
                <div class="form-group add-space">
                    <div class="">
                        <button type="submit" class="pull-right btn btn-primary">
                            @lang('default.buttons.next')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('js')
    <script>
        $(document).ready(function () {

            $(window).keydown(function (event) {
                if (event.keyCode === 13) {
                    event.preventDefault();
                    return false;
                }
            });

            <?php
                $myFeatures = $building->buildingFeatures()->forMe()->first();
                $prevBt = \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'building_type_id') ?? "";
                $prevBy = \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'build_year') ?? "";
            ?>
            var previous_bt = "{{ $prevBt }}";
            var previous_by = "{{ $prevBy }}";

            $("form.form-horizontal").on('submit', function () {
                // Store the current value on focus and on change
                var bt_now = $("select#building_type_id").val();
                var by_now = $("input#build_year").val();

                if (bt_now !== previous_bt || by_now !== previous_by){
                    if (previous_bt === "" && previous_by === "" || confirm('{{ \App\Helpers\Translation::translate('building-detail.warning.title') }}')) {
                        @if(App::environment('local'))
                        console.log("Building Type was changed, but it was empty or a wanted change. Proceed.");
                        @endif
                    }
                    else {
                        // the dirty class by areYouSure is removed on submit.
                        // set it back to prevent confusion for the user
                        $(this).addClass('dirty');
                        // don't submit
                        return false;
                    }
                }
            });
        });
    </script>
@endpush