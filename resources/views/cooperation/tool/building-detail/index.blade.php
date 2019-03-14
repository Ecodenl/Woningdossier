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

                @component('cooperation.tool.components.step-question', ['id' => 'building_type_id', 'translation' => 'building-detail.building-type.what-type'])
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

                @endcomponent
            </div>

            <div class="col-md-6">

                @component('cooperation.tool.components.step-question', ['id' => 'build_year', 'translation' => 'building-detail.building-type.what-building-year', 'required' => true])

                    @component('cooperation.tool.components.input-group',
                    ['inputType' => 'input', 'userInputValues' => $building->buildingFeatures()->forMe()->get(), 'userInputColumn' => 'build_year'])
                        <input id="build_year" type="text" class="form-control" name="build_year"
                               value="{{ old('build_year', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'build_year')) }}"
                               required autofocus>
                    @endcomponent

                @endcomponent
            </div>
        </div>
        @if(!\App\helpers\HoomdossierSession::isUserObserving())
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
        @endif
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
                $prevBt = \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'building_type_id') ?? '';
                $prevBy = \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'build_year') ?? '';
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