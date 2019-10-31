@extends('cooperation.tool.layout')

@section('step_content')

    <form class="form-horizontal" method="POST" id="main-form" action="{{ route('cooperation.tool.general-data.usage.store') }}" autocomplete="off">
        {{ csrf_field() }}

        <div class="row">
            <div class="col-sm-12">
                <h4>@lang('general-data/usage.water-gas.title.title')</h4>
            </div>

            <div class="col-lg-4 col-md-12">
                @component('cooperation.tool.components.step-question', ['id' => 'user_energy_habits.resident_count', 'translation' => 'general-data.data-about-usage.total-citizens', 'required' => true])
                    @component('cooperation.tool.components.input-group',
                    ['inputType' => 'input', 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'resident_count'])
                        <input type="text" id="resident_count" class="form-control" value="{{ old('user_energy_habits.resident_count', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'resident_count')) }}" name="user_energy_habits[resident_count]" required="required">
                    @endcomponent
                @endcomponent
            </div>

            <div class="col-lg-5 col-md-12">
                @component('cooperation.tool.components.step-question', ['id' => 'water_comfort', 'translation' => 'general-data.data-about-usage.comfortniveau-warm-tapwater', 'required' => false])
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
                @component('cooperation.tool.components.step-question', ['id' => 'cook_gas', 'translation' => 'general-data.data-about-usage.cooked-on-gas', 'required' => true])

                    <div class="input-group input-source-group">
                        <label class="radio-inline">
                            <input type="radio" name="cook_gas" required="required"
                                   @if(old('cook_gas', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'cook_gas')) == 1) checked
                                   @endif value="1">{{\App\Helpers\Translation::translate('general.options.yes.title')}}
                            {{--<input type="radio" name="cook_gas" @if(old('cook_gas') == 1) checked @elseif(isset($energyHabit) && $energyHabit->cook_gas == 1) checked @endif  value="1">@lang('woningdossier.cooperation.radiobutton.yes')--}}
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="cook_gas" required="required"
                                   @if(old('cook_gas', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'cook_gas')) == 2) checked
                                   @endif value="2">{{\App\Helpers\Translation::translate('general.options.no.title')}}
                        </label>
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default dropdown-toggle"
                                    data-toggle="dropdown"><span class="caret"></span></button>
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
                                        <li class="change-input-value"
                                            data-input-source-short="{{$userInputValue->inputSource()->first()->short}}"
                                            data-input-value="{{ $value }}"><a
                                                    href="#">{{ $userInputValue->getInputSourceName() }}
                                                : {{ $trans }}</a></li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                @endcomponent
            </div>
        </div>
    </form>
@endsection

@push('js')
@endpush