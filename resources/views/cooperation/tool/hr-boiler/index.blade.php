@extends('cooperation.tool.layout')

@section('step_title', \App\Helpers\Translation::translate('high-efficiency-boiler.title.title'))


@section('step_content')
    <form  method="POST" action="{{ route('cooperation.tool.high-efficiency-boiler.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}
        @include('cooperation.tool.includes.interested', [
            'translation' => 'high-efficiency-boiler.index.interested-in-improvement', 'interestedInType' => \App\Models\Step::class, 'interestedInId' => $currentStep->id,
        ])
        <div id="start-information">
            <div class="row">
                <div class="col-sm-6">
                    @component('cooperation.tool.components.step-question', ['id' => 'user_energy_habits.amount_gas', 'translation' => 'high-efficiency-boiler.current-gas-usage', 'required' => false])

                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'input', 'userInputValues' => $userEnergyHabitsOrderedOnInputSourceCredibility, 'userInputColumn' => 'amount_gas'])
                            <span class="input-group-addon">m<sup>3</sup></span>
                            <input type="text" id="amount_gas" name="user_energy_habits[amount_gas]" class="form-control"
                                   value="{{ old('user_energy_habits.amount_gas', Hoomdossier::getMostCredibleValueFromCollection($userEnergyHabitsOrderedOnInputSourceCredibility, 'amount_gas', 0)) }}">
                        @endcomponent

                    @endcomponent

                </div>
                <div class="col-sm-6">
                    @component('cooperation.tool.components.step-question', ['id' => 'user_energy_habits.resident_count', 'translation' => 'high-efficiency-boiler.resident-count', 'required' => false])
                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'input', 'userInputValues' => $userEnergyHabitsOrderedOnInputSourceCredibility, 'userInputColumn' => 'resident_count'])
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input type="text" id="resident_count" name="user_energy_habits[resident_count]" class="form-control"
                                   value="{{ old('user_energy_habits.resident_count', Hoomdossier::getMostCredibleValueFromCollection($userEnergyHabitsOrderedOnInputSourceCredibility, 'resident_count', 0)) }}">
                        @endcomponent

                    @endcomponent
                </div>
            </div>
        </div>
        <div id="intro-questions">
            <div class="row">
                <div id="boiler-options">
                    <div class="col-sm-6">
                        @component('cooperation.tool.components.step-question', ['id' => 'building_services.service_value_id', 'translation' => 'high-efficiency-boiler.boiler-type', 'required' => false])

                            @component('cooperation.tool.components.input-group',
                            ['inputType' => 'select', 'inputValues' => $boilerTypes, 'userInputValues' => $buildingServicesOrderedOnInputSourceCredibility, 'userInputColumn' => 'service_value_id'])
                                <select id="high_efficiency_boiler_id" class="form-control"
                                        name="building_services[service_value_id]">
                                    @foreach($boilerTypes as $boilerType)
                                        <option @if(old('building_services.service_value_id', Hoomdossier::getMostCredibleValueFromCollection($buildingServicesOrderedOnInputSourceCredibility, 'service_value_id')) == $boilerType->id) selected="selected"
                                                @endif value="{{ $boilerType->id }}">{{ $boilerType->value }}</option>
                                    @endforeach
                                </select>
                            @endcomponent

                        @endcomponent

                    </div>


                </div>
                <div class="col-sm-6">
                    @component('cooperation.tool.components.step-question', ['id' => 'building_services.extra.date', 'translation' => 'high-efficiency-boiler.boiler-placed-date', 'required' => true])

                        <?php
                            $default = ($installedBoiler instanceof \App\Models\BuildingService && is_array($installedBoiler->extra) && array_key_exists('date', $installedBoiler->extra)) ? $installedBoiler->extra['date'] : '';
                        ?>

                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'input', 'userInputValues' => $buildingServicesOrderedOnInputSourceCredibility, 'userInputColumn' => 'extra.date'])
                            <input type="text" required class="form-control"
                                   value="{{ old('building_services.extra.date', Hoomdossier::getMostCredibleValueFromCollection($buildingServicesOrderedOnInputSourceCredibility, 'extra.date')) }}"
                                   name="building_services[extra][date]">
                        @endcomponent

                    @endcomponent


                </div>
            </div>
        </div>
        <div class="row advice">
            <div class="col-sm-12 col-md-8 col-md-offset-2">
                <div class="alert alert-info show" role="alert">
                    <p id="boiler-advice"></p>
                </div>
            </div>
        </div>
        <div id="indication-for-costs">
            <hr>
            @include('cooperation.tool.includes.section-title', [
                'translation' => 'high-efficiency-boiler.indication-for-costs.title',
                'id' => 'indication-for-costs-title'
            ])

            <div id="costs" class="row">
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.gas', ['translation' => 'high-efficiency-boiler.index.costs.gas'])
                </div>
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.co2', ['translation' => 'high-efficiency-boiler.index.costs.co2'])
                </div>
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.savings-in-euro',[
                                'translation' => 'floor-insulation.index.savings-in-euro'
                            ])
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                        @component('cooperation.tool.components.step-question', ['id' => 'indicative-replacement', 'translation' => 'high-efficiency-boiler.indication-for-costs.indicative-replacement', 'required' => false])
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                <input type="text" id="replace_year" class="form-control disabled" disabled="">
                            </div>
                        @endcomponent
                </div>
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.indicative-costs',[
                                'translation' => 'floor-insulation.index.indicative-costs'
                            ])
                </div>
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.comparable-rent',[
                                'translation' => 'floor-insulation.index.comparable-rent'
                            ])
                </div>
            </div>
        </div>


        @include('cooperation.tool.includes.comment', [
             'translation' => 'high-efficiency-boiler.index.specific-situation'
         ])

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">{{\App\Helpers\Translation::translate('general.download.title')}}</div>
                    <div class="panel-body">
                        <ol>
                            <li><a download=""
                                   href="{{asset('storage/hoomdossier-assets/Maatregelblad_CV-ketel.pdf')}}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_CV-ketel.pdf')))))}}</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('js')
    <script>
        $(document).ready(function () {


            $("select, input[type=radio], input[type=text]").change(formChange);

            function formChange() {
                var form = $(this).closest("form").serialize();
                $.ajax({
                    type: "POST",
                    url: '{{ route('cooperation.tool.high-efficiency-boiler.calculate', [ 'cooperation' => $cooperation ]) }}',
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

            $('form').find('*').filter(':input:visible:first').trigger('change');

        });
    </script>
@endpush
