@extends('cooperation.frontend.layouts.tool')

@section('step_title', __('heater.title.title'))

@section('content')
    <form  method="POST" id="heater-form"
          action="{{ route('cooperation.tool.heater.store', compact('cooperation')) }}">
        @csrf
        
        @include('cooperation.tool.includes.interested', [
            'translation' => 'heater.index.interested-in-improvement', 
            'interestedInType' => \App\Models\Step::class, 'interestedInId' => $currentStep->id,
        ])
        <div id="heater">
            <div class="flex flex-row flex-wrap w-full sm:pad-x-6">
                <div class="w-full sm:w-1/3">
                    @component('cooperation.tool.components.step-question', [
                        'id' => 'user_energy_habits.water_comfort_id',
                        'translation' => 'heater.comfort-level-warm-tap-water', 'required' => false
                    ])
                        @slot('sourceSlot')
                            @include('cooperation.tool.components.source-list', [
                                'inputType' => 'select', 'inputValues' => $comfortLevels,
                                'userInputValues' => $energyHabitsOrderedOnInputSourceCredibility,
                                'userInputColumn' => 'water_comfort_id'
                            ])
                        @endslot

                        @component('cooperation.frontend.layouts.components.alpine-select')
                            <select id="user_energy_habits_water_comfort_id" class="form-input"
                                    name="user_energy_habits[water_comfort_id]">
                                @foreach($comfortLevels as $comfortLevel)
                                    <option @if(old('user_energy_habits.water_comfort_id', Hoomdossier::getMostCredibleValueFromCollection($energyHabitsOrderedOnInputSourceCredibility, 'water_comfort_id')) == $comfortLevel->id) selected
                                            @endif value="{{ $comfortLevel->id }}">
                                        {{ $comfortLevel->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endcomponent
                    @endcomponent
                </div>

                <div class="w-full sm:w-1/3">
                    @component('cooperation.tool.components.step-question', [
                        'id' => 'building_heaters.pv_panel_orientation_id', 
                        'translation' => 'heater.pv-panel-orientation-id', 'required' => false
                    ])
                        @slot('sourceSlot')
                            @include('cooperation.tool.components.source-list', [
                                'inputType' => 'select', 'inputValues' => $collectorOrientations, 
                                'userInputValues' => $heatersOrderedOnInputSourceCredibility, 
                                'userInputColumn' => 'pv_panel_orientation_id'
                            ])
                        @endslot
                    
                        @component('cooperation.frontend.layouts.components.alpine-select')
                            <select id="building_heaters_pv_panel_orientation_id" class="form-input"
                                    name="building_heaters[pv_panel_orientation_id]">
                                @foreach($collectorOrientations as $collectorOrientation)
                                    <option @if(old('building_heaters.pv_panel_orientation_id', Hoomdossier::getMostCredibleValueFromCollection($heatersOrderedOnInputSourceCredibility, 'pv_panel_orientation_id')) == $collectorOrientation->id) selected="selected"
                                            @endif value="{{ $collectorOrientation->id }}">
                                        {{ $collectorOrientation->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endcomponent
                    @endcomponent
                </div>

                <div class="w-full sm:w-1/3">
                    @component('cooperation.tool.components.step-question', [
                        'id' => 'building_heaters.angle', 'translation' => 'heater.angle', 'required' => false
                    ])
                        @slot('sourceSlot')
                            @include('cooperation.tool.components.source-list', [
                                'inputType' => 'select', 
                                'inputValues' => \App\Helpers\KeyFigures\Heater\KeyFigures::getAngles(), 
                                'userInputValues' => $heatersOrderedOnInputSourceCredibility, 
                                'userInputColumn' => 'angle'
                            ])
                        @endslot
                    
                        @component('cooperation.frontend.layouts.components.alpine-select', ['prepend' => '&deg;'])
                            <select id="building_heaters_angle" class="form-input" name="building_heaters[angle]">
                                @foreach(\App\Helpers\KeyFigures\Heater\KeyFigures::getAngles() as $angle)
                                    <option @if(old('building_heaters.angle', Hoomdossier::getMostCredibleValueFromCollection($heatersOrderedOnInputSourceCredibility, 'angle')) == $angle) selected
                                            @endif value="{{ $angle }}">
                                        {{ $angle }}
                                    </option>
                                @endforeach
                            </select>
                        @endcomponent
                    @endcomponent
                </div>
            </div>

            <div id="estimated-usage">
                <hr>
                @include('cooperation.tool.includes.section-title', [
                    'translation' => 'heater.estimated-usage.title',
                    'id' => 'estimated-usage-title'
                ])

                <div id="consumption" class="flex flex-row flex-wrap w-full">

                    <div class="w-full sm:w-1/2 sm:pr-3">
                        @component('cooperation.tool.components.step-question', [
                            'id' => 'consumption-water', 'translation' => 'heater.consumption-water', 
                            'required' => false, 'withInputSource' => false,
                        ])
                            <span class="input-group-prepend">
                                @lang('general.unit.liter.title') / @lang('general.unit.day.title')
                            </span>
                            <input type="text" id="consumption_water" class="form-input disabled" disabled=""
                                   value="0">
                        @endcomponent
                    </div>

                    <div class="w-full sm:w-1/2 sm:pl-3">
                        @component('cooperation.tool.components.step-question', [
                            'id' => 'consumption-gas', 'translation' => 'heater.consumption-gas', 'required' => false,
                            'withInputSource' => false,
                        ])
                            <span class="input-group-prepend">
                                m<sup>3</sup> / @lang('general.unit.year.title')
                            </span>
                            <input type="text" id="consumption_gas" class="form-input disabled" disabled=""
                                   value="0">
                        @endcomponent
                    </div>
                </div>
            </div>

            <div id="system-specs">
                <hr>
                @include('cooperation.tool.includes.section-title', [
                    'translation' => 'heater.system-specs',
                    'id' => 'system-specs-title'
                ])

                <div id="consumption" class="flex flex-row flex-wrap w-full">

                    <div class="w-full sm:w-1/2 sm:pr-3">
                        @component('cooperation.tool.components.step-question', [
                            'id' => 'size-boiler', 'translation' => 'heater.size-boiler', 'required' => false,
                            'withInputSource' => false,
                        ])
                            <span class="input-group-prepend">@lang('general.unit.liter.title')</span>
                            <input type="text" id="size_boiler" class="form-input disabled" disabled=""
                                   value="0">
                        @endcomponent
                    </div>

                    <div class="w-full sm:w-1/2 sm:pl-3">
                        @component('cooperation.tool.components.step-question', [
                            'id' => 'size-collector', 'translation' => 'heater.size-collector', 'required' => false,
                            'withInputSource' => false,
                        ])
                            <span class="input-group-prepend">m<sup>2</sup></span>
                            <input type="text" id="size_collector" class="form-input disabled" disabled=""
                                   value="0">
                        @endcomponent
                    </div>

                </div>
            </div>

            <div id="indication-for-costs">
                <hr>
                @include('cooperation.tool.includes.section-title', [
                    'translation' => 'heater.indication-for-costs.title',
                    'id' => 'indication-for-costs-title'
                ])

                <div class="flex flex-row flex-wrap w-full">
                    <div class="w-full sm:w-1/2 sm:pr-3">
                            @component('cooperation.tool.components.step-question', [
                                'id' => 'production-heat',
                                'translation' => 'heater.indication-for-costs.production-heat', 'required' => false,
                                'withInputSource' => false,
                            ])
                                <span class="input-group-prepend">kWh / @lang('general.unit.year.title')</span>
                                <input type="text" id="production_heat" class="form-input disabled" disabled=""
                                       value="0">
                            @endcomponent
                    </div>
                    <div class="w-full sm:w-1/2 sm:pl-3">
                        @component('cooperation.tool.components.step-question', [
                            'id' => 'percentage-consumption',
                            'translation' => 'heater.indication-for-costs.percentage-consumption', 'required' => false,
                            'withInputSource' => false,
                        ])
                            <span class="input-group-prepend">%</span>
                            <input type="text" id="percentage_consumption" class="form-input disabled"
                                   disabled="" value="0">
                        @endcomponent
                    </div>

                </div>

            </div>
            <div class="flex flex-row flex-wrap w-full sm:pad-x-6">
                <div class="w-full sm:w-1/3">
                    @include('cooperation.layouts.indication-for-costs.gas', [
                        'translation' => 'heater.index.costs.gas'
                    ])
                </div>
                <div class="w-full sm:w-1/3">
                    @include('cooperation.layouts.indication-for-costs.co2', [
                        'translation' => 'heater.index.costs.co2'
                    ])
                </div>
                <div class="w-full sm:w-1/3">
                    @include('cooperation.layouts.indication-for-costs.savings-in-euro',[
                        'translation' => 'heater.index.savings-in-euro'
                    ])
                </div>
            </div>
            <div class="flex flex-row flex-wrap w-full">
                <div class="w-full sm:w-1/2 sm:pr-3">
                    @include('cooperation.layouts.indication-for-costs.indicative-costs',[
                        'translation' => 'heater.index.indicative-costs'
                    ])
                </div>
                <div class="w-full sm:w-1/2 sm:pl-3">
                    @include('cooperation.layouts.indication-for-costs.comparable-rent',[
                        'translation' => 'heater.index.comparable-rent'
                    ])
                </div>
            </div>

            <div class="flex flex-row flex-wrap w-full system-performance">
                <div class="w-full md:w-8/12 md:ml-2/12">
                    @component('cooperation.frontend.layouts.parts.alert', [
                        'color' => 'yellow',
                        'dismissible' => false
                    ])
                        <p id="performance-text" class="text-yellow"></p>
                    @endcomponent
                </div>
            </div>


            @include('cooperation.tool.includes.comment', [
                 'translation' => 'heater.index.specific-situation'
             ])
            
            @component('cooperation.tool.components.panel', [
                'label' => __('default.buttons.download'),    
            ])
                <ol>
                    <li><a download=""
                           href="{{asset('storage/hoomdossier-assets/Maatregelblad_Zonneboiler.pdf')}}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Zonneboiler.pdf')))))}}</a>
                    </li>
                </ol>
            @endcomponent
        </div>
    </form>
@endsection

@push('js')
    <script>
        $(document).ready(function () {


            $("select, input[type=radio], input[type=text]").change(formChange);

            function formChange() {
                var form = $('#heater-form').serialize();
                $.ajax({
                    type: "POST",
                    url: '{{ route('cooperation.tool.heater.calculate', compact('cooperation')) }}',
                    data: form,
                    success: function (data) {
                        if (data.hasOwnProperty('consumption')) {
                            $("input#consumption_water").val(hoomdossierRound(data.consumption.water));
                            $("input#consumption_gas").val(hoomdossierRound(data.consumption.gas));
                        }
                        if (data.hasOwnProperty('specs')) {
                            $("input#size_boiler").val(hoomdossierRound(data.specs.size_boiler));
                            $("input#size_collector").val(data.specs.size_collector);
                        }
                        if (data.hasOwnProperty('production_heat')) {
                            $("input#production_heat").val(hoomdossierRound(data.production_heat));
                        }
                        if (data.hasOwnProperty('percentage_consumption')) {
                            $("input#percentage_consumption").val(hoomdossierRound(data.percentage_consumption));
                        }
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
                        if (data.hasOwnProperty('performance')) {
                            let $systemAlert = $(".system-performance .alert");
                            let $systemText = $(".system-performance .alert #performance-text");

                            $systemText.html("<strong>" + data.performance.text + "</strong>");
                            $systemAlert.removeClass('text-red border-red text-yellow border-yellow text-green border-green').addClass(`text-${data.performance.alert} border-${data.performance.alert}`);
                            $systemText.removeClass('text-red text-yellow text-green').addClass(`text-${data.performance.alert}`);
                            $(".system-performance").show();
                        } else {
                            $("#performance-text").html("");
                            $(".system-performance").hide();
                        }

                        @if(App::environment('local'))
                        console.log(data);
                        @endif
                    }
                });
            }

            formChange();
        });
    </script>
@endpush

