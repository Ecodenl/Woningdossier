@extends('cooperation.frontend.layouts.tool')

@section('step_title', __('high-efficiency-boiler.title.title'))

@section('content')
    <form  method="POST" id="high-efficiency-boiler-form"
           action="{{ route('cooperation.tool.high-efficiency-boiler.store', compact('cooperation')) }}">
        @csrf
        @include('cooperation.tool.includes.interested', [
            'translation' => 'high-efficiency-boiler.index.interested-in-improvement', 
            'interestedInType' => \App\Models\Step::class, 'interestedInId' => $currentStep->id,
        ])
        <div id="start-information">
            <div class="flex flex-row flex-wrap w-full">
                <div class="w-full sm:w-1/2 sm:pr-3">
                    @component('cooperation.tool.components.step-question', [
                        'id' => 'user_energy_habits.amount_gas',
                        'translation' => 'high-efficiency-boiler.current-gas-usage', 'required' => false
                    ])
                        @slot('sourceSlot')
                            @include('cooperation.tool.components.source-list', [
                                'inputType' => 'input', 
                                'userInputValues' => $userEnergyHabitsOrderedOnInputSourceCredibility, 
                                'userInputColumn' => 'amount_gas'
                            ])
                        @endslot
                        
                        <span class="input-group-prepend">m<sup>3</sup></span>
                        <input type="text" id="amount_gas" name="user_energy_habits[amount_gas]" class="form-input"
                               value="{{ old('user_energy_habits.amount_gas', Hoomdossier::getMostCredibleValueFromCollection($userEnergyHabitsOrderedOnInputSourceCredibility, 'amount_gas', 0)) }}">
                    @endcomponent
                </div>
                <div class="w-full sm:w-1/2 sm:pl-3">
                    @component('cooperation.tool.components.step-question', [
                        'id' => 'user_energy_habits.resident_count',
                        'translation' => 'high-efficiency-boiler.resident-count', 'required' => false
                    ])
                        @slot('sourceSlot')
                            @include('cooperation.tool.components.source-list', [
                                'inputType' => 'input',
                                'userInputValues' => $userEnergyHabitsOrderedOnInputSourceCredibility,
                                'userInputColumn' => 'resident_count'
                            ])
                        @endslot

                        <span class="input-group-prepend"><i class="icon-sm icon-persons-one"></i></span>
                        <input type="text" id="resident_count" name="user_energy_habits[resident_count]" class="form-input"
                               value="{{ old('user_energy_habits.resident_count', Hoomdossier::getMostCredibleValueFromCollection($userEnergyHabitsOrderedOnInputSourceCredibility, 'resident_count', 0)) }}">
                    @endcomponent
                </div>
            </div>
        </div>
        <div id="intro-questions">
            <div class="flex flex-row flex-wrap w-full">
                <div id="boiler-options" class="w-full sm:w-1/2 sm:pr-3">
                    @component('cooperation.tool.components.step-question', [
                        'id' => 'building_services.service_value_id',
                        'translation' => 'high-efficiency-boiler.boiler-type', 'required' => false
                    ])
                        @slot('sourceSlot')
                            @include('cooperation.tool.components.source-list', [
                                'inputType' => 'select', 'inputValues' => $boilerTypes, 'userInputValues' => $buildingServicesOrderedOnInputSourceCredibility, 'userInputColumn' => 'service_value_id'])
                        @endslot

                        @component('cooperation.frontend.layouts.components.alpine-select')
                            <select id="high_efficiency_boiler_id" class="form-input"
                                    name="building_services[service_value_id]">
                                @foreach($boilerTypes as $boilerType)
                                    <option @if(old('building_services.service_value_id', Hoomdossier::getMostCredibleValueFromCollection($buildingServicesOrderedOnInputSourceCredibility, 'service_value_id')) == $boilerType->id) selected="selected"
                                            @endif value="{{ $boilerType->id }}">
                                        {{ $boilerType->value }}
                                    </option>
                                @endforeach
                            </select>
                        @endcomponent
                    @endcomponent
                </div>
                <div class="w-full sm:w-1/2 sm:pl-3">
                    @component('cooperation.tool.components.step-question', [
                        'id' => 'building_services.extra.date',
                        'translation' => 'high-efficiency-boiler.boiler-placed-date',
                        'required' => true
                    ])
                        <?php
                            $default = ($installedBoiler instanceof \App\Models\BuildingService && is_array($installedBoiler->extra) && array_key_exists('date', $installedBoiler->extra)) ? $installedBoiler->extra['date'] : '';
                        ?>
                        @slot('sourceSlot')
                            @include('cooperation.tool.components.source-list', [
                                'inputType' => 'input',
                                'userInputValues' => $buildingServicesOrderedOnInputSourceCredibility,
                                'userInputColumn' => 'extra.date'
                            ])
                        @endslot

                        <input type="text" required class="form-input"
                               value="{{ old('building_services.extra.date', Hoomdossier::getMostCredibleValueFromCollection($buildingServicesOrderedOnInputSourceCredibility, 'extra.date')) }}"
                               name="building_services[extra][date]">
                    @endcomponent
                </div>
            </div>
        </div>
        <div class="flex flex-row flex-wrap w-full advice">
            <div class="w-full md:w-8/12 md:ml-2/12">
                @component('cooperation.frontend.layouts.parts.alert', [
                    'color' => 'blue-800',
                    'dismissible' => false,
                ])
                    <p id="boiler-advice" class="text-blue-800"></p>
                @endcomponent
            </div>
        </div>
        <div id="indication-for-costs">
            <hr>
            @include('cooperation.tool.includes.section-title', [
                'translation' => 'high-efficiency-boiler.indication-for-costs.title',
                'id' => 'indication-for-costs-title'
            ])

            <div id="costs" class="flex flex-row flex-wrap w-full sm:pad-x-6">
                <div class="w-full sm:w-1/3">
                    @include('cooperation.layouts.indication-for-costs.gas', [
                        'translation' => 'high-efficiency-boiler.index.costs.gas'
                    ])
                </div>
                <div class="w-full sm:w-1/3">
                    @include('cooperation.layouts.indication-for-costs.co2', [
                        'translation' => 'high-efficiency-boiler.index.costs.co2'
                    ])
                </div>
                <div class="w-full sm:w-1/3">
                    @include('cooperation.layouts.indication-for-costs.savings-in-euro',[
                        'translation' => 'floor-insulation.index.savings-in-euro'
                    ])
                </div>
            </div>
            <div class="flex flex-row flex-wrap w-full sm:pad-x-6">
                <div class="w-full sm:w-1/3">
                    @component('cooperation.tool.components.step-question', [
                        'id' => 'indicative-replacement',
                        'translation' => 'high-efficiency-boiler.indication-for-costs.indicative-replacement',
                        'required' => false, 'withInputSource' => false,
                    ])
                        <span class="input-group-prepend"><i class="icon-sm icon-timer"></i></span>
                        <input type="text" id="replace_year" class="form-input disabled" disabled="">
                    @endcomponent
                </div>
                <div class="w-full sm:w-1/3">
                    @include('cooperation.layouts.indication-for-costs.indicative-costs',[
                        'translation' => 'floor-insulation.index.indicative-costs'
                    ])
                </div>
                <div class="w-full sm:w-1/3">
                    @include('cooperation.layouts.indication-for-costs.comparable-rent',[
                        'translation' => 'floor-insulation.index.comparable-rent'
                    ])
                </div>
            </div>
        </div>

        @include('cooperation.tool.includes.comment', [
             'translation' => 'high-efficiency-boiler.index.specific-situation'
         ])

        @component('cooperation.tool.components.panel', [
            'label' => __('default.buttons.download')
        ])
            <ol>
                <li><a download=""
                       href="{{asset('storage/hoomdossier-assets/Maatregelblad_CV-ketel.pdf')}}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_CV-ketel.pdf')))))}}</a>
                </li>
            </ol>
        @endcomponent
    </form>
@endsection

@push('js')
    <script>
        $(document).ready(function () {

            $("select, input[type=radio], input[type=text]").change(formChange);

            function formChange() {
                let form = $('#high-efficiency-boiler-form').serialize();
                $.ajax({
                    type: "POST",
                    url: '{{ route('cooperation.tool.high-efficiency-boiler.calculate', compact('cooperation')) }}',
                    data: form,
                    success: function (data) {
                        if (data.boiler_advice) {
                            $("#boiler-advice").html("<strong>" + data.boiler_advice + "</strong>");
                            $(".advice").show();
                        } else {
                            $("#boiler-advice").html("");
                            $(".advice").hide();
                        }

                        if (data.hasOwnProperty('savings_gas')) {
                            console.log(data.savings_gas);
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
                        if (data.hasOwnProperty('replace_year')) {
                            $("input#replace_year").val(data.replace_year);
                        }
                        if (data.hasOwnProperty('interest_comparable')) {
                            $("input#interest_comparable").val(hoomdossierNumberFormat(data.interest_comparable, '{{ app()->getLocale() }}', 1));
                        }
                        @if(App::environment('local'))
                        console.log(data);
                        @endif
                    }
                });
            }

            formChange();
            // $('.form-input:visible:enabled').first().trigger('change');
        });
    </script>
@endpush
