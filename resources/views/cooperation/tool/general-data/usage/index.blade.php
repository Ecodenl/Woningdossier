@extends('cooperation.tool.layout')

@section('step_content')

    <form class="form-horizontal" method="POST" id="main-form" action="{{ route('cooperation.tool.general-data.usage.store') }}" autocomplete="off">
        {{ csrf_field() }}

        {{--tapwater and cook inputs--}}
        <div class="row">
            <div class="col-sm-12">
                <h4>@lang('cooperation/tool/general-data/usage.index.water-gas.title.title')</h4>
            </div>

            <div class="col-lg-4 col-md-12">
                @component('cooperation.tool.components.step-question', ['id' => 'user_energy_habits.resident_count', 'translation' => 'cooperation/tool/general-data/usage.index.water-gas.resident-count', 'required' => true])
                    @component('cooperation.tool.components.input-group',
                    ['inputType' => 'input', 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'resident_count'])
                        <input type="text" id="resident_count" class="form-control" value="{{ old('user_energy_habits.resident_count', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'resident_count')) }}" name="user_energy_habits[resident_count]" required="required">
                    @endcomponent
                @endcomponent
            </div>

            <div class="col-lg-5 col-md-12">
                @component('cooperation.tool.components.step-question', ['id' => 'water_comfort', 'translation' => 'cooperation/tool/general-data/usage.index.water-gas.water-comfort', 'required' => false])
                    @component('cooperation.tool.components.input-group',
                    ['inputType' => 'select', 'inputValues' => $comfortLevelsTapWater, 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'water_comfort_id'])
                        <select id="water_comfort" class="form-control" name="user_energy_habits[water_comfort_id]">
                            @foreach($comfortLevelsTapWater as $comfortLevelTapWater)
                                <option @if(old('user_energy_habits.water_comfort_id', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'water_comfort_id')) == $comfortLevelTapWater->id)
                                            selected="selected"
                                        @endif value="{{ $comfortLevelTapWater->id }}">{{ $comfortLevelTapWater->name }}</option>
                            @endforeach
                        </select>
                    @endcomponent
                @endcomponent
            </div>

            <div class="col-lg-3 col-md-12">
                @component('cooperation.tool.components.step-question', ['id' => 'cook_gas', 'translation' => 'cooperation/tool/general-data/usage.index.water-gas.cook-gas', 'required' => true])

                    <div class="input-group input-source-group">
                        <label class="radio-inline">
                            <input type="radio" name="user_energy_habits[cook_gas]" required="required" @if(old('cook_gas', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'cook_gas')) == 1) checked @endif value="1">{{\App\Helpers\Translation::translate('general.options.yes.title')}}
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="user_energy_habits[cook_gas]" required="required" @if(old('cook_gas', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'cook_gas')) == 2) checked @endif value="2">{{\App\Helpers\Translation::translate('general.options.no.title')}}
                        </label>
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <?php
                                    // we need to check if there is a answer from one input source
                                    $hasAnswerCookGas = $userEnergyHabitsForMe->contains('cook_gas', '!=', '');
                                ?>
                                @if(!$hasAnswerCookGas)
                                    @include('cooperation.tool.includes.no-answer-available')
                                @else
                                    @foreach($userEnergyHabitsForMe as $userInputValue)
                                        <?php
                                        // simple check if the user input column has dots, if it does it means we have to get a array from the row so we use the array_get method
                                        $value = $userInputValue->cook_gas;
                                        if (1 === $value) {
                                            $trans = __('woningdossier.cooperation.radiobutton.yes');
                                        } elseif (2 === $value) {
                                            $trans = __('woningdossier.cooperation.radiobutton.no');
                                        }
                                        ?>
                                        <li class="change-input-value" data-input-source-short="{{$userInputValue->inputSource()->first()->short}}" data-input-value="{{ $value }}"><a href="#">{{ $userInputValue->getInputSourceName() }}: {{ $trans }}</a></li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                @endcomponent
            </div>
        </div>

        {{--energy usage thermostat high / low and hours--}}
        <div class="row mt-10">
            <div class="col-sm-12">
                <h4>@lang('cooperation/tool/general-data/usage.index.heating-habits.title.title')</h4>


                {{-- degree high low --}}
                <div class="row mt-20">
                    <div class="col-sm-6">
                        @component('cooperation.tool.components.step-question', ['id' => 'user_energy_habits.thermostat_high', 'translation' => 'cooperation/tool/general-data/usage.index.heating-habits.thermostat-high', 'required' => false])
                            @component('cooperation.tool.components.input-group',
                            ['inputType' => 'input', 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'thermostat_high', 'needsFormat' => true])
                                <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.degrees.title')}}</span>
                                <input type="text" id="thermostat_high" class="form-control"
                                       value="{{ old('user_energy_habits.thermostat_high', \App\Helpers\NumberFormatter::format(\App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'thermostat_high', 20), 1)) }}"
                                       name="user_energy_habits[thermostat_high]">
                            @endcomponent

                        @endcomponent

                    </div>

                    <div class="col-sm-6">
                        @component('cooperation.tool.components.step-question', ['id' => 'user_energy_habits.thermostat_low', 'translation' => 'cooperation/tool/general-data/usage.index.heating-habits.thermostat-low', 'required' => false])
                            @component('cooperation.tool.components.input-group',
                            ['inputType' => 'input', 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'thermostat_low', 'needsFormat' => true])
                                <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.degrees.title')}}</span>
                                <input id="thermostat_low" type="text" class="form-control" name="user_energy_habits[thermostat_low]" value="{{ old('user_energy_habits.thermostat_low', \App\Helpers\NumberFormatter::format(\App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'thermostat_low', 16), 1)) }}">
                            @endcomponent
                        @endcomponent

                    </div>
                </div>

                {{--hours termostat high--}}
                <div class="row mt-20">
                    <div class="col-sm-12">
                        @component('cooperation.tool.components.step-question', ['id' => 'user_energy_habits.hours_high', 'translation' => 'cooperation/tool/general-data/usage.index.heating-habits.hours-high', 'required' => false])

                            <?php
                                $hours = range(1, 24);
                                $selectedHours = old('user_energy_habits.hours_high', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'hours_high', 12));
                                // We have to prepend the value so the key => value pairs are in order for the input group addon
                                $inputValues = $hours;
                                array_unshift($inputValues, __('woningdossier.cooperation.radiobutton.not-important'));
                            ?>
                            @component('cooperation.tool.components.input-group', ['inputType' => 'select', 'inputValues' => $inputValues, 'userInputValues' => $userEnergyHabitsForMe, 'userInputModel' => 'UserEnergyHabit', 'userInputColumn' => 'hours_high'])
                                <span class="input-group-addon">@lang('woningdossier.cooperation.tool.unit.hours')</span>
                                <select id="hours_high" class="form-control" name="user_energy_habits[hours_high]">
                                    @foreach($hours as $hour)
                                        <option @if($hour === $selectedHours) selected
                                                @endif value="{{ $hour }}">{{ $hour }}</option>
                                    @endforeach
                                    <option @if(0 === $selectedHours) selected @endif value="0">
                                        @lang('woningdossier.cooperation.radiobutton.not-important')
                                    </option>
                                </select>
                            @endcomponent
                        @endcomponent
                    </div>
                </div>

                {{--situation first and second floor--}}
                <div class="row mt-20">
                    <div class="col-sm-6">
                    @component('cooperation.tool.components.step-question', ['id' => 'user_energy_habits.heating_first_floor', 'translation' => 'cooperation/tool/general-data/usage.index.heating-habits.heating-first-floor', 'required' => false])
                        <?php

                        $bhDefault = $buildingHeatings->where('is_default', '=', true)->first();
                        if ($bhDefault instanceof \App\Models\BuildingHeating) {
                            $defaultHFF = $bhDefault->id;
                        }

                        $selectedHFF = old('user_energy_habits.heating_first_floor', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'heating_first_floor', $defaultHFF));


                        ?>

                        @component('cooperation.tool.components.input-group',['inputType' => 'select', 'inputValues' => $buildingHeatings, 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'heating_first_floor'])
                            <select id="heating_first_floor" class="form-control" name="user_energy_habits[heating_first_floor]">
                                @foreach($buildingHeatings as $buildingHeating)
                                    <option @if($buildingHeating->id == $selectedHFF) selected="selected"
                                            @endif value="{{ $buildingHeating->id}}">{{ $buildingHeating->name }}</option>
                                @endforeach
                            </select>
                        @endcomponent

                    @endcomponent
                </div>

                    <div class="col-sm-6">
                        @component('cooperation.tool.components.step-question', ['id' => 'user_energy_habits.heating_second_floor', 'translation' => 'cooperation/tool/general-data/usage.index.heating-habits.heating-second-floor', 'required' => false])
                            <?php

                            $bhDefault = $buildingHeatings->where('is_default', '=', true)->first();
                            if ($bhDefault instanceof \App\Models\BuildingHeating) {
                                $defaultHSF = $bhDefault->id;
                            }

                            $selectedHSF = old('user_energy_habits.heating_second_floor', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'heating_second_floor', $defaultHSF));

                            ?>

                            @component('cooperation.tool.components.input-group', ['inputType' => 'select', 'inputValues' => $buildingHeatings, 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'heating_second_floor'])
                                <select id="heating_second_floor" class="form-control"
                                        name="user_energy_habits[heating_second_floor]">
                                    @foreach($buildingHeatings as $buildingHeating)
                                        <option @if($buildingHeating->id == $selectedHSF) selected="selected"
                                                @endif value="{{ $buildingHeating->id }}">{{ $buildingHeating->name }}</option>
                                    @endforeach
                                </select>
                            @endcomponent
                        @endcomponent

                    </div>

                </div>
            </div>
        </div>

        {{-- heating habits, gas and electricity --}}
        <div class="row mt-10">
            <div class="col-sm-12">
                <h4>@lang('cooperation/tool/general-data/usage.index.energy-usage.title.title')</h4>

                <div class="row mt-20">
                    <div class="col-sm-6">
                        @component('cooperation.tool.components.step-question', ['id' => 'user_energy_habits.amount_electricity', 'translation' => 'cooperation/tool/general-data/usage.index.energy-usage.amount-electricity', 'required' => true])
                            @component('cooperation.tool.components.input-group',
                            ['inputType' => 'input', 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'amount_electricity'])
                                <span class="input-group-addon">@lang('woningdossier.cooperation.tool.unit.kwh')</span>
                                <input id="user_energy_habits[amount_electricity]" type="text" value="{{ old('user_energy_habits.amount_electricity', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'amount_electricity')) }}" class="form-control" name="user_energy_habits[amount_electricity]" required="required">
                            @endcomponent
                        @endcomponent

                    </div>
                    <div class="col-sm-6">
                        @component('cooperation.tool.components.step-question', ['id' => 'user_energy_habits.amount_gas', 'translation' => 'cooperation/tool/general-data/usage.index.energy-usage.gas-usage', 'required' => true])
                            @component('cooperation.tool.components.input-group',
                            ['inputType' => 'input', 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'amount_gas'])
                                <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.cubic-meters.title')}}</span>
                                <input type="text" value="{{ old('user_energy_habits.amount_gas', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'amount_gas')) }}" class="form-control" name="user_energy_habits[amount_gas]" required="required">
                            @endcomponent
                        @endcomponent
                    </div>
                </div>
            </div>
        </div>


        @include('cooperation.tool.includes.comment', [
            'columnName' => 'step_comments[comment]',
            'translation' => 'cooperation/tool/general-data/usage.index.comment'
        ])
    </form>
@endsection

@push('js')
@endpush