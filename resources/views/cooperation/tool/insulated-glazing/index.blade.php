@extends('cooperation.tool.layout')

@section('step_title', \App\Helpers\Translation::translate('insulated-glazing.title.title'))


@section('step_content')

    <?php
    // we only need this for the titles above the main inputs
    // we dont want a title above the hr3p-frames
    $titles = [
        7 => 'glass-in-lead',
        8 => 'hrpp-glass-only',
        9 => 'hrpp-glass-frames',
    ];
    ?>
    <form method="POST"
          action="{{ route('cooperation.tool.insulated-glazing.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}

        <div id="main-glass-questions">
            @foreach($measureApplications as $i => $measureApplication)
                @if($i > 0 && array_key_exists($measureApplication->id, $titles))
                    <hr>
                @endif
                <?php
                if (array_key_exists($measureApplication->id, $buildingInsulatedGlazingsForMe)) {
                    $currentMeasureBuildingInsulatedGlazingForMe = $buildingInsulatedGlazingsForMe[$measureApplication->id];
                } else {
                    $currentMeasureBuildingInsulatedGlazingForMe = [];
                }
                    $buildingInsulatedGlazingsOrderedOnInputSourceCredibility = Hoomdossier::orderRelationShipOnInputSourceCredibility(
                        $building->currentInsulatedGlazing()->where('measure_application_id', $measureApplication->id)
                    )->get();
                ?>

                <div class="row">
                    <div class="col-sm-12">
                        {{-- since there is no title / subtitle for the hr3p --}}
                        @if(array_key_exists($measureApplication->id, $titles))
                            @include('cooperation.tool.includes.section-title', [
                              'translation' => 'insulated-glazing.subtitles.'.$measureApplication->short,
                                'id' => 'insulated-glazing-subtitles-'.$measureApplication->short
                            ])

                        @endif

                        @component('cooperation.tool.components.step-question', ['id' => 'user_interests.'.$measureApplication->id, 'translation' => 'insulated-glazing.'.$measureApplication->short.'.title', 'required' => false])

                            @component('cooperation.tool.components.input-group', ['inputType' => 'select', 'inputValues' => $interests, 'userInputValues' => $userInterestsForMe->where('interested_in_id', $measureApplication->id),  'userInputColumn' => 'interest_id'])
                                <input type="hidden"
                                       name="user_interests[{{ $measureApplication->id }}][interested_in_type]"
                                       value="{{get_class($measureApplication)}}">
                                <select id="{{ $measureApplication->id }}" class="user-interest form-control"
                                        name="user_interests[{{ $measureApplication->id }}][interest_id]">
                                    <?php
                                        /** @var \Illuminate\Support\Collection $interests */
                                        $userSelectedInterest = $userInterests[$measureApplication->id] ?? null;
                                        $userInput = old("user_interests.{$measureApplication->id}.interest_id", $userSelectedInterest)
                                    ?>
                                    @foreach($interests as $interest)
                                        {{-- calculate_value 4 is the default --}}
                                        <option data-calculate-value="{{$interest->calculate_value}}"
                                                @if(!empty($userInput))
                                                    @if($interest->id == $userInput)
                                                        selected="selected"
                                                    @endif
                                                {{--when no answer is given select the default interest--}}
                                                @elseif(is_null($userSelectedInterest) && $interest->calculate_value == 4)
                                                   selected="selected"
                                                @endif
                                                value="{{ $interest->id }}">{{ $interest->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @endcomponent

                        @endcomponent

                    </div>

                    <div class="values">


                        <div class="col-sm-3">
                            @component('cooperation.tool.components.step-question', ['id' => 'building_insulated_glazings.' . $measureApplication->id . '.insulating_glazing_id', 'translation' => 'insulated-glazing.'.$measureApplication->short.'.current-glass', 'required' => false])
                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'select', 'inputValues' => $insulatedGlazings, 'userInputValues' => $currentMeasureBuildingInsulatedGlazingForMe ,'userInputColumn' => 'insulating_glazing_id'])
                                    <select class="form-control"
                                            name="building_insulated_glazings[{{ $measureApplication->id }}][insulating_glazing_id]">
                                        @foreach($insulatedGlazings as $insulateGlazing)
                                            <option @if($insulateGlazing->id == old('building_insulated_glazings.' . $measureApplication->id . '.insulating_glazing_id', Hoomdossier::getMostCredibleValueFromCollection($buildingInsulatedGlazingsOrderedOnInputSourceCredibility, 'insulating_glazing_id'))) selected
                                                    @endif value="{{ $insulateGlazing->id }}">{{ $insulateGlazing->name }}</option>
                                            {{--<option @if($insulateGlazing->id == old('building_insulated_glazings.' . $measureApplication->id . '.insulated_glazing_id') || (array_key_exists($measureApplication->id, $buildingInsulatedGlazings) && $buildingInsulatedGlazings[$measureApplication->id]->insulating_glazing_id == $insulateGlazing->id)) selected @endif value="{{ $insulateGlazing->id }}">{{ $insulateGlazing->name }}</option>--}}
                                        @endforeach
                                    </select>
                                @endcomponent

                            @endcomponent

                        </div>
                        <div class="col-sm-3">
                            @component('cooperation.tool.components.step-question',
                            ['id' => 'building_insulated_glazings.' . $measureApplication->id . '.building_heating_id', 'translation' => 'insulated-glazing.'.$measureApplication->short.'.rooms-heated', 'required' => false])

                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'select', 'inputValues' => $heatings, 'userInputValues' => $currentMeasureBuildingInsulatedGlazingForMe ,'userInputColumn' => 'building_heating_id'])
                                    <select class="form-control"
                                            name="building_insulated_glazings[{{ $measureApplication->id }}][building_heating_id]">
                                        @foreach($heatings as $heating)
                                            <option @if($heating->id == old('building_insulated_glazings.' . $measureApplication->id . '.building_heating_id', Hoomdossier::getMostCredibleValueFromCollection($buildingInsulatedGlazingsOrderedOnInputSourceCredibility, 'building_heating_id'))) selected
                                                    @endif value="{{ $heating->id }}">{{ $heating->name }}</option>
                                            {{--<option @if($heating->id == old('building_insulated_glazings.' . $measureApplication->id . '.building_heating_id') || (array_key_exists($measureApplication->id, $buildingInsulatedGlazings) && $buildingInsulatedGlazings[$measureApplication->id]->building_heating_id == $heating->id)) selected="selected" @endif value="{{ $heating->id }}">{{ $heating->name }}</option>--}}
                                        @endforeach
                                    </select>
                                @endcomponent

                            @endcomponent

                        </div>
                        <div class="col-sm-3">
                            @component('cooperation.tool.components.step-question', [
                                'id' => 'building_insulated_glazings.' . $measureApplication->id . '.m2',
                                'name' => 'building_insulated_glazings.' . $measureApplication->id . '.m2',
                                'translation' => 'insulated-glazing.'.$measureApplication->short.'.m2', 'required' => false
                            ])
                                <span> *</span>

                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'input', 'userInputValues' => $currentMeasureBuildingInsulatedGlazingForMe ,'userInputColumn' => 'm2'])
                                    <input type="text"
                                           name="building_insulated_glazings[{{ $measureApplication->id }}][m2]"
                                           value="{{ old('building_insulated_glazings.'.$measureApplication->id.'.m2', Hoomdossier::getMostCredibleValueFromCollection($buildingInsulatedGlazingsOrderedOnInputSourceCredibility, 'm2')) }}"
                                           class="form-control">
                                    {{--<input type="text" name="building_insulated_glazings[{{ $measureApplication->id }}][m2]" value="{{ old('building_insulated_glazings.' . $measureApplication->id . '.m2', array_key_exists($measureApplication->id, $buildingInsulatedGlazings) ? $buildingInsulatedGlazings[$measureApplication->id]->m2 : '') }}" class="form-control">--}}
                                @endcomponent

                            @endcomponent

                        </div>

                        <div class="col-sm-3">
                            @component('cooperation.tool.components.step-question', [
                                'id' => 'building_insulated_glazings.' . $measureApplication->id . '.windows',
                                'translation' => 'insulated-glazing.'.$measureApplication->short.'.window-replace',
                                'name' => 'building_insulated_glazings.' . $measureApplication->id . '.windows',
                                'required' => false
                            ])
                                <span> *</span>

                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'input', 'userInputValues' => $currentMeasureBuildingInsulatedGlazingForMe ,'userInputColumn' => 'windows'])
                                    <input type="text"
                                           name="building_insulated_glazings[{{ $measureApplication->id }}][windows]"
                                           value="{{ old('building_insulated_glazings.' . $measureApplication->id . '.windows', Hoomdossier::getMostCredibleValueFromCollection($buildingInsulatedGlazingsOrderedOnInputSourceCredibility, 'windows')) }}"
                                           class="form-control">
                                    {{--<input type="text" name="building_insulated_glazings[{{ $measureApplication->id }}][windows]" value="{{ old('building_insulated_glazings.' . $measureApplication->id . '.windows', array_key_exists($measureApplication->id, $buildingInsulatedGlazings) ? $buildingInsulatedGlazings[$measureApplication->id]->windows : '') }}" class="form-control">--}}
                                @endcomponent
                            @endcomponent
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <hr>

        <div id="paint-work">
            <div class="row">
                <div class="col-sm-12">
                    <hr>
                    @include('cooperation.tool.includes.section-title', [
                 'translation' => 'insulated-glazing.paint-work.title',
                 'id' => 'paint-work-title'
             ])
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    @component('cooperation.tool.components.step-question', ['id' => 'building_features.window_surface', 'translation' => 'insulated-glazing.windows-surface', 'required' => false])

                        @component('cooperation.tool.components.input-group',
                       ['inputType' => 'input', 'userInputValues' => $buildingFeaturesForMe, 'userInputColumn' => 'window_surface'])
                            <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.square-meters.title')}}</span>
                            <input type="text" name="building_features[window_surface]"
                                   value="{{ old('building_features.window_surface', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'window_surface')) }}"
                                   class="form-control">
                        @endcomponent
                    @endcomponent

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                @component('cooperation.tool.components.step-question', ['id' => 'building_elements.'.$frames->id, 'translation' => 'insulated-glazing.paint-work.which-frames', 'required' => false])

                    @component('cooperation.tool.components.input-group',
                    ['inputType' => 'select', 'inputValues' => $frames->values()->orderBy('order')->get(), 'userInputValues' => $building->getBuildingElementsForMe('frames'), 'userInputColumn' => 'element_value_id'])
                        <select class="form-control" name="building_elements[{{$frames->id}}]">
                            @foreach($frames->values()->orderBy('order')->get() as $frameValue)
                                <option @if(old('building_elements.'.$frames->id, \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingElements()->where('element_id', $frames->id), 'element_value_id')) == $frameValue->id) selected="selected"
                                        @endif value="{{ $frameValue->id }}">{{ $frameValue->value }}</option>
                                {{--<option @if($frameValue->id == old('building_elements.frames')  || ($building->getBuildingElement('frames') instanceof \App\Models\BuildingElement && $building->getBuildingElement('frames')->element_value_id == $frameValue->id)) selected @endif value="{{ $frameValue->id }}">{{ $frameValue->value }}</option>--}}
                            @endforeach
                        </select>
                    @endcomponent

                @endcomponent

            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                @component('cooperation.tool.components.step-question',
                ['id' => 'building_elements.'.$woodElements->id.'.wood-elements', 'translation' => 'insulated-glazing.paint-work.other-wood-elements', 'required' => false])



                    <?php
                    // TODO: should do something with a component
                    // the current problem is there are only 2 places where checkboxes are used and those are used in a different way
                    ?>
                    <div class="input-group input-source-group">
                        @foreach($woodElements->values()->orderBy('order')->get() as $woodElement)
                            <label for="building_elements.{{ $woodElement->id }}"
                                   class="checkbox-inline">
                                <input
                                        @if(old('building_elements.' . $woodElements->id . '.' . $woodElement->id,\App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingElements()->where('element_id', $woodElements->id)->where('element_value_id', $woodElement->id), 'element_value_id')))
                                        checked="checked"
                                        @endif
                                        type="checkbox" id="building_elements.{{ $woodElement->id }}"
                                        value="{{$woodElement->id}}"
                                        name="building_elements[{{ $woodElements->id }}][{{$woodElement->id}}]">
                                {{ $woodElement->value }}
                            </label>
                        @endforeach
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                <img src="{{asset('images/input-source-icon.png')}}" alt=""
                                     style="height: 22px; display: block;">
                            </button>
                            <ul class="dropdown-menu">
                                <?php
                                // check if there is a answer available from a input source.
                                $hasAnswerWoodElements = $building->buildingElements()
                                    ->withoutGlobalScope(\App\Scopes\GetValueScope::class)
                                    ->where('element_id', $woodElements->id)
                                    ->get()
                                    ->contains('element_value_id', '!=', '');
                                ?>
                                @if(!$hasAnswerWoodElements)
                                    @include('cooperation.tool.includes.no-answer-available')
                                @else
                                    @foreach ($woodElements->values()->orderBy('order')->get() as $woodElement)
                                        <?php
                                        $myWoodElements = $myBuildingElements->where('element_id', $woodElements->id)->where('element_value_id', $woodElement->id);

                                        ?>
                                        @foreach($myWoodElements as $myWoodElement)
                                            @if (!is_null($myWoodElement) && $myWoodElement->element_value_id == $woodElement->id)
                                                <li class="change-input-value" data-input-value="{{$woodElement->id}}"
                                                    data-input-source-short="{{$myWoodElement->inputSource()->first()->short}}">
                                                    <a href="#">{{$myWoodElement->getInputSourceName()}}
                                                        : {{$woodElement->value}}</a>
                                                </li>
                                            @endif
                                        @endforeach
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                @endcomponent
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                @component('cooperation.tool.components.step-question',
                ['id' => 'building_paintwork_statuses.last_painted_year', 'translation' => 'insulated-glazing.paint-work.last-paintjob', 'required' => false])

                    @component('cooperation.tool.components.input-group',
                           ['inputType' => 'input', 'userInputValues' => $buildingPaintworkStatusesOrderedOnInputSourceCredibility ,'userInputColumn' => 'last_painted_year'])
                        <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.year.title')}}</span>
                        <input type="text" name="building_paintwork_statuses[last_painted_year]"
                               class="form-control"
                               value="{{ old('building_paintwork_statuses.last_painted_year', Hoomdossier::getMostCredibleValueFromCollection($buildingPaintworkStatusesOrderedOnInputSourceCredibility, 'last_painted_year')) }}">
                    @endcomponent
                @endcomponent

            </div>
            <div class="col-sm-6">
                @component('cooperation.tool.components.step-question',
                ['id' => 'building_paintwork_statuses.paintwork_status_id', 'translation' => 'insulated-glazing.paint-work.paint-damage-visible', 'required' => false])

                    @component('cooperation.tool.components.input-group',
                    ['inputType' => 'select', 'inputValues' => $paintworkStatuses, 'userInputValues' => $buildingPaintworkStatusesOrderedOnInputSourceCredibility, 'userInputColumn' => 'paintwork_status_id'])
                        <select class="form-control" name="building_paintwork_statuses[paintwork_status_id]">
                            @foreach($paintworkStatuses as $paintworkStatus)
                                <option @if($paintworkStatus->id == old('building_paintwork_statuses.paintwork_status_id', Hoomdossier::getMostCredibleValueFromCollection($buildingPaintworkStatusesOrderedOnInputSourceCredibility, 'paintwork_status_id'))) selected
                                        @endif value="{{ $paintworkStatus->id }}">{{ $paintworkStatus->name }}</option>
                            @endforeach
                        </select>
                    @endcomponent

                @endcomponent

            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                @component('cooperation.tool.components.step-question', ['id' => 'building_paintwork_statuses.wood_rot_status_id', 'translation' => 'insulated-glazing.paint-work.wood-rot-visible', 'required' => false])

                    @component('cooperation.tool.components.input-group',
                    ['inputType' => 'select', 'inputValues' => $woodRotStatuses, 'userInputValues' => $buildingPaintworkStatusesOrderedOnInputSourceCredibility, 'userInputColumn' => 'wood_rot_status_id'])
                        <select class="form-control" name="building_paintwork_statuses[wood_rot_status_id]">
                            @foreach($woodRotStatuses as $woodRotStatus)
                                <option @if($woodRotStatus->id == old('building_paintwork_statuses.wood_rot_status_id', Hoomdossier::getMostCredibleValueFromCollection($buildingPaintworkStatusesOrderedOnInputSourceCredibility, 'wood_rot_status_id'))) selected
                                        @endif value="{{ $woodRotStatus->id }}">{{ $woodRotStatus->name }}</option>
                                {{--<option @if($woodRotStatus->id == old('building_paintwork_statuses.wood_rot_status_id') || ($building->currentPaintworkStatus instanceof \App\Models\BuildingPaintworkStatus && $building->currentPaintworkStatus->wood_rot_status_id == $woodRotStatus->id) ) selected @endif value="{{ $woodRotStatus->id }}">{{ $woodRotStatus->name }}</option>--}}
                            @endforeach
                        </select>
                    @endcomponent

                @endcomponent

            </div>
        </div>

        <div id="indication-for-costs">
            <hr>
            @include('cooperation.tool.includes.section-title', ['translation' => 'insulated-glazing.costs.cost-and-benefits', 'id' => 'cost-and-benefits',])

            <div id="costs" class="row">
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.gas', ['translation' => 'insulated-glazing.index.costs.gas'])
                </div>
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.co2', ['translation' => 'insulated-glazing.index.costs.co2'])
                </div>
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.savings-in-euro', [
                        'translation' => 'insulated-glazing.index.savings-in-euro'
                    ])
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.indicative-costs', [
                        'translation' => 'insulated-glazing.index.indicative-costs'
                    ])
                </div>
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.comparable-rent', [
                        'translation' => 'insulated-glazing.index.comparable-rent'
                    ])
                </div>
            </div>
        </div>

        <div id="taking-into-account">
            <hr>
            @include('cooperation.tool.includes.section-title', ['translation' => 'insulated-glazing.taking-into-account.title', 'id' => 'taking-into-account',])

            <div class="row">
                <div class="col-sm-6">
                    @component('cooperation.tool.components.step-question', ['id' => 'paintwork', 'translation' => 'insulated-glazing.taking-into-account.paintwork',])
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                            <input type="text" id="paintwork_costs" class="form-control disabled" disabled=""
                                   value="0">
                        </div>
                    @endcomponent
                </div>
                <div class="col-sm-6">
                    @component('cooperation.tool.components.step-question', ['id' => 'paintwork_year', 'translation' => 'insulated-glazing.taking-into-account.paintwork_year',])
                        <div class="input-group">
                            <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.year.title')}}</span>
                            <input type="text" id="paintwork_year" class="form-control disabled" disabled=""
                                   value="0">
                        </div>
                    @endcomponent
                </div>
            </div>
        </div>

        @include('cooperation.tool.includes.comment', [
            'translation' => 'insulated-glazing.index.specific-situation'
        ])

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">@lang('default.buttons.download')</div>
                    <div class="panel-body">
                        <ol>
                            <li><a download=""
                                   href="{{ asset('storage/hoomdossier-assets/Maatregelblad_Glasisolatie.pdf') }}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Glasisolatie.pdf')))))}}</a>
                            </li>
                            <li><a download=""
                                   href="{{ asset('storage/hoomdossier-assets/Maatregelblad_Kierdichting_bouwdelen.pdf') }}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Kierdichting_bouwdelen.pdf')))))}}</a>
                            </li>
                            <li><a download=""
                                   href="{{ asset('storage/hoomdossier-assets/Maatregelblad_Kierdichting_ramen_en_deuren.pdf') }}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Kierdichting_ramen_en_deuren.pdf')))))}}</a>
                            </li>

                        </ol>
                    </div>
                </div>
                <hr>

            </div>
        </div>
    </form>

@endsection

@push('js')
    <script>
        $(document).ready(function () {


            $("select, input[type=radio], input[type=text], input[type=checkbox]").change(function () {

                var form = $(this).closest("form").serialize();
                $.ajax({
                    type: "POST",
                    url: '{{ route('cooperation.tool.insulated-glazing.calculate', [ 'cooperation' => $cooperation ]) }}',
                    data: form,
                    success: function (data) {

                        if (data.hasOwnProperty('savings_gas')) {
                            $("input#savings_gas").val(hoomdossierRound(data.savings_gas));
                        }
                        if (data.hasOwnProperty('savings_co2')) {
                            $("input#savings_co2").val(hoomdossierRound(data.savings_co2));
                        }
                        if (data.hasOwnProperty('savings_money')) {
                            $("input#savings_money").val(hoomdossierRound(data.savings_money));
                        }
                        if (data.hasOwnProperty('cost_indication')) {
                            $("input#cost_indication").val(hoomdossierRound(data.cost_indication));
                        }
                        if (data.hasOwnProperty('interest_comparable')) {
                            $("input#interest_comparable").val(hoomdossierNumberFormat(data.interest_comparable, '{{ app()->getLocale() }}', 1));
                        }
                        if (data.hasOwnProperty('paintwork')) {
                            $("input#paintwork_costs").val(hoomdossierRound(data.paintwork.costs));
                        }
                        if (data.hasOwnProperty('paintwork')) {
                            $("input#paintwork_year").val(data.paintwork.year);
                        }

                        @if(App::environment('local'))
                        console.log(data);
                        @endif

                    }
                });
            });

            $('.user-interest').change(function () {
                // the input field
                var userInterest = $(this);
                // the user interest calculate value
                var userInterestCalculateValue = userInterest.find('option:selected').data('calculate-value');

                // div that holds the inputs (m2 and windows)
                var valueElements = userInterest.parents('.row').first().find('.values');

                if (userInterestCalculateValue === 4 || userInterestCalculateValue === 5) {
                    valueElements.hide();
                    // clear the inputs, if the user filled in a faulty input it will still be send to the backend
                    // validation fails, inputs are hidden and the user would not know whats wrong
                    valueElements.find('input').val(null)
                } else {
                    valueElements.show();
                }
            });

            $('.user-interest').trigger('change');

            // Trigger the change event so it will load the data
            //$('form').find('*').filter(':input:visible:first').trigger('change');
            $('.panel-body form').find('*').filter(':input:visible:first').trigger('change');
        });

    </script>
@endpush