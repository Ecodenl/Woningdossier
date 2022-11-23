@extends('cooperation.frontend.layouts.tool')

@section('step_title', __('solar-panels.title.title'))

@section('content')
    <form method="POST" id="solar-panels-form"
          action="{{ route('cooperation.tool.solar-panels.store', ['cooperation' => $cooperation]) }}">
        @csrf

        @include('cooperation.tool.includes.considerable', ['considerable' => $currentStep])

        @php
            $toolQuestion = $hasSolarPanelsToolQuestion;
            $humanReadableAnswer = null;
            $disabled = false;
            $masterInputSource = \App\Models\InputSource::findByShort('master');
            $answer = old("filledInAnswers.{$toolQuestion->id}", $building->getAnswer($masterInputSource, $toolQuestion));
        @endphp

        <div id="solar-panels" x-data="{hasSolarPanels: '{{$answer}}' }">
            <div class="flex flex-row flex-wrap w-full">
                <div class="w-full sm:w-1/2 sm:pr-3">
                    @component('cooperation.tool.components.step-question', [
                        'id' => 'user_energy_habits.amount_electricity',
                        'translation' => 'solar-panels.electra-usage', 'required' => false
                    ])
                        @slot('sourceSlot')
                            @include('cooperation.tool.components.source-list', [
                                'inputType' => 'input',
                                'userInputValues' => $energyHabitsOrderedOnInputSourceCredibility,
                                'userInputColumn' => 'amount_electricity'
                            ])
                        @endslot

                        <span class="input-group-prepend">kWh / @lang('general.unit.year.title')</span>
                        <input type="number" min="0" class="form-input" name="user_energy_habits[amount_electricity]"
                               value="{{ old('user_energy_habits.amount_electricity', Hoomdossier::getMostCredibleValueFromCollection($energyHabitsOrderedOnInputSourceCredibility, 'amount_electricity', 0)) }}"/>
                        {{--<input type="number" min="0" class="form-input" name="user_energy_habits[amount_electricity]" value="{{ old('user_energy_habits.amount_electricity', $amountElectricity) }}" />--}}
                    @endcomponent
                </div>
                <div class="w-full sm:w-1/2 sm:pl-3">
                    @component('cooperation.tool.components.step-question', [
                        'id' => 'building_pv_panels.peak_power',
                        'translation' => 'solar-panels.peak-power', 'required' => false
                    ])
                        @slot('sourceSlot')
                            @include('cooperation.tool.components.source-list', [
                                'inputType' => 'select',
                                'inputValues' => \App\Helpers\KeyFigures\PvPanels\KeyFigures::getPeakPowers(),
                                'userInputValues' => $pvPanelsOrderedOnInputSourceCredibility,
                                'userInputColumn' => 'peak_power'
                            ])
                        @endslot

                        @component('cooperation.frontend.layouts.components.alpine-select', ['prepend' => 'Wp'])
                            <select id="building_pv_panels_peak_power" class="form-input"
                                    name="building_pv_panels[peak_power]">
                                @foreach(\App\Helpers\KeyFigures\PvPanels\KeyFigures::getPeakPowers() as $peakPower)
                                    <option @if(old('building_pv_panels.peak_power', Hoomdossier::getMostCredibleValueFromCollection($pvPanelsOrderedOnInputSourceCredibility, 'peak_power') == $peakPower)) selected
                                            @endif value="{{ $peakPower }}">
                                        {{ $peakPower }}
                                    </option>
                                @endforeach
                            </select>
                        @endcomponent
                    @endcomponent
                </div>
            </div>

            <div class="flex flex-row flex-wrap w-full advice">
                <div class="w-full md:w-8/12 md:ml-2/12">
                    @component('cooperation.frontend.layouts.parts.alert', [
                        'color' => 'blue-800',
                        'dismissible' => false,
                    ])
                        <p id="solar-panels-advice" class="text-blue-800"></p>
                    @endcomponent
                </div>
            </div>

            <div class="w-full">
                @component('cooperation.frontend.layouts.components.form-group', [
                    'class' => 'form-group-heading',
                    // 'defaultInputSource' => 'resident',
                    // so we give the option to replace something in the question title
                    'label' => __($toolQuestion->name . (is_null($toolQuestion->forSpecificInputSource) ? '' : " ({$toolQuestion->forSpecificInputSource->name})"), ['name' => $humanReadableAnswer]),
                    'inputName' => "filledInAnswers.{$toolQuestion->id}",
                    'withInputSource' => ! $disabled,
                ])
                    @slot('sourceSlot')
                        @include('cooperation.sub-step-templates.parts.source-slot-values', [
                            'values' => $building->getAnswerForAllInputSources($toolQuestion),
                            'toolQuestion' => $toolQuestion,
                        ])
                    @endslot

                    @slot('modalBodySlot')
                        <p>
                            {!! $toolQuestion->help_text !!}
                        </p>
                    @endslot
                    <div class="w-full flex space-x-4">
                        @php
                            $questionValues = \App\Helpers\QuestionValues\QuestionValue::init($cooperation, $toolQuestion)
                               ->forInputSource($masterInputSource)
                               ->forBuilding($building)
                               ->withCustomEvaluation()
                               ->getQuestionValues();
                        @endphp
                        @foreach($questionValues as $toolQuestionValue)
                            @php
                                $id = $toolQuestionValue['short'] ?? $toolQuestionValue['calculate_value'] ?? $toolQuestionValue['value'];
                            @endphp
                            <div class="radio-wrapper media-wrapper">
                                <input type="radio" id="{{$id}}" x-model="hasSolarPanels"
                                       name="filledInAnswers[{{$toolQuestion['id']}}]"
                                       @if($toolQuestionValue['value'] === $answer) checked="checked" @endif
                                       value="{{$toolQuestionValue['value']}}"
                                       @if($disabled) disabled="disabled" @endif>
                                <label for="{{$id}}">
                                <span class="media-icon-wrapper">
                                    <i class="{{$toolQuestionValue['extra']['icon'] ?? ''}}"></i>
                                </span>
                                    <span class="checkmark"></span>
                                    <span>{{$toolQuestionValue['name']}}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endcomponent
            </div>

            <div class="flex flex-row flex-wrap w-full sm:pad-x-6" x-cloak x-show="hasSolarPanels == 'yes'">
                <div class="w-full sm:w-1/2">
                    @component('cooperation.tool.components.step-question', [
                        'id' => 'building_pv_panels.total_installed_power', 'translation' => App\Models\ToolQuestion::findByShort('total-installed-power')->name,

                        'required' => false
                    ])
                        @slot('sourceSlot')
                            @include('cooperation.tool.components.source-list', [
                                'inputType' => 'input', 'userInputValues' => $pvPanelsOrderedOnInputSourceCredibility,
                                'userInputColumn' => 'building_pv_panels.total_installed_power'
                            ])
                        @endslot

                        <span class="input-group-prepend">@lang('general.unit.wp.title')</span>
                        <input type="text" class="form-input" name="building_pv_panels[total_installed_power]"
                               value="{{ old('building_pv_panels.total_installed_power', Hoomdossier::getMostCredibleValueFromCollection($pvPanelsOrderedOnInputSourceCredibility, 'total_installed_power', 0)) }}"/>
                    @endcomponent
                </div>
                <div class="w-full sm:w-1/2">
                    @component('cooperation.tool.components.step-question', [
                        'id' => "building_services.{$totalSolarPanelService->id}.extra.year", 'translation' => App\Models\ToolQuestion::findByShort('solar-panels-placed-date')->name,
                        'required' => false
                    ])
                        @slot('sourceSlot')
                            @include('cooperation.tool.components.source-list', [
                                'inputType' => 'input', 'userInputValues' => $totalSolarPanelBuildingServicesOrderedOnInputSourceCredibility,
                                'userInputColumn' => 'extra.year'
                            ])
                        @endslot

                        <span class="input-group-prepend">@lang('general.unit.year.title')</span>
                        <input type="text" class="form-input"
                               name="building_services[{{$totalSolarPanelService->id}}][extra][year]"
                               value="{{ old("building_services.{$totalSolarPanelService->id}.extra.year", Hoomdossier::getMostCredibleValueFromCollection($totalSolarPanelBuildingServicesOrderedOnInputSourceCredibility, 'extra.year')) }}"/>
                    @endcomponent
                </div>
            </div>

            <div class="flex flex-row flex-wrap w-full">
                <div class="w-full sm:w-1/2" x-cloak x-show="hasSolarPanels == 'yes'">
                    @component('cooperation.tool.components.step-question', [
                        'id' => "building_services.{$totalSolarPanelService->id}.extra.value", 'translation' => App\Models\ToolQuestion::findByShort('solar-panel-count')->name,
                        'required' => false
                    ])
                        @slot('sourceSlot')
                            @include('cooperation.tool.components.source-list', [
                                'inputType' => 'input', 'userInputValues' => $totalSolarPanelBuildingServicesOrderedOnInputSourceCredibility,
                                'userInputColumn' => 'extra.value'
                            ])
                        @endslot

                        <span class="input-group-prepend">@lang('general.unit.pieces.title')</span>
                        <input type="text" class="form-input"
                               name="building_services[{{$totalSolarPanelService->id}}][extra][value]"
                               value="{{ old("building_services.{$totalSolarPanelService->id}.extra.value", Hoomdossier::getMostCredibleValueFromCollection($totalSolarPanelBuildingServicesOrderedOnInputSourceCredibility, 'extra.value', 0)) }}"/>
                    @endcomponent
                </div>
                <div class="w-full sm:w-1/2" x-bind:class="{ 'sm:pl-6': hasSolarPanels == 'yes' }">
                    @component('cooperation.tool.components.step-question', [
                        'id' => 'building_pv_panels.number', 'translation' => 'solar-panels.number',
                        'required' => false
                    ])
                        @slot('sourceSlot')
                            @include('cooperation.tool.components.source-list', [
                                'inputType' => 'input', 'userInputValues' => $pvPanelsOrderedOnInputSourceCredibility,
                                'userInputColumn' => 'number'
                            ])
                        @endslot

                        <span class="input-group-prepend">@lang('general.unit.pieces.title')</span>
                        <input type="text" class="form-input" name="building_pv_panels[number]"
                               value="{{ old('building_pv_panels.number', Hoomdossier::getMostCredibleValueFromCollection($pvPanelsOrderedOnInputSourceCredibility, 'number', 0)) }}"/>
                    @endcomponent
                </div>
            </div>

            <div class="flex flex-row flex-wrap w-full sm:pad-x-6">
                <div class="w-full sm:w-1/2">
                    @component('cooperation.tool.components.step-question', [
                        'id' => 'building_pv_panels.pv_panel_orientation_id',
                        'translation' => 'solar-panels.pv-panel-orientation-id', 'required' => false
                    ])
                        @slot('sourceSlot')
                            @include('cooperation.tool.components.source-list', [
                                'inputType' => 'select', 'inputValues' => $pvPanelOrientations,
                                'userInputValues' => $pvPanelsOrderedOnInputSourceCredibility,
                                'userInputColumn' => 'pv_panel_orientation_id'
                            ])
                        @endslot

                        @component('cooperation.frontend.layouts.components.alpine-select')
                            <select id="building_pv_panels_pv_panel_orientation_id" class="form-input"
                                    name="building_pv_panels[pv_panel_orientation_id]">
                                @foreach($pvPanelOrientations as $pvPanelOrientation)
                                    <option @if(old('building_pv_panels.pv_panel_orientation_id', Hoomdossier::getMostCredibleValueFromCollection($pvPanelsOrderedOnInputSourceCredibility, 'pv_panel_orientation_id')) == $pvPanelOrientation->id) selected="selected"
                                            @endif value="{{ $pvPanelOrientation->id }}">
                                        {{ $pvPanelOrientation->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endcomponent
                    @endcomponent
                </div>

                <div class="w-full sm:w-1/2">
                    @component('cooperation.tool.components.step-question', [
                        'id' => 'building_pv_panels.angle', 'translation' => 'solar-panels.angle', 'required' => false
                    ])
                        @slot('sourceSlot')
                            @include('cooperation.tool.components.source-list', [
                                'inputType' => 'select',
                                'inputValues' => \App\Helpers\KeyFigures\PvPanels\KeyFigures::getAngles(),
                                'userInputValues' => $pvPanelsOrderedOnInputSourceCredibility,
                                'userInputColumn' => 'angle'
                            ])
                        @endslot

                        @component('cooperation.frontend.layouts.components.alpine-select', ['prepend' => '&deg;'])
                            <select id="building_pv_panels_angle" class="form-input"
                                    name="building_pv_panels[angle]">
                                @foreach(\App\Helpers\KeyFigures\PvPanels\KeyFigures::getAngles() as $angle)
                                    <option @if(old('building_pv_panels.angle', Hoomdossier::getMostCredibleValueFromCollection($pvPanelsOrderedOnInputSourceCredibility, 'angle')) == $angle) selected="selected"
                                            @endif value="{{ $angle }}">
                                        {{ $angle }}
                                    </option>
                                @endforeach
                            </select>
                        @endcomponent
                    @endcomponent
                </div>
            </div>

            <div class="flex flex-row flex-wrap w-full total-power">
                <div class="w-full md:w-8/12 md:ml-2/12">
                    @component('cooperation.frontend.layouts.parts.alert', [
                        'color' => 'blue-800',
                        'dismissible' => false,
                    ])
                        <p id="solar-panels-total-power" class="text-blue-800"></p>
                    @endcomponent
                </div>
            </div>

            <div id="indication-for-costs">
                <hr>
                @include('cooperation.tool.includes.section-title', [
                    'translation' => 'solar-panels.indication-for-costs.title',
                    'id' => 'indication-for-costs',
                ])

                <div id="costs" class="flex flex-row flex-wrap w-full sm:pad-x-6">
                    <div class="w-full sm:w-1/3">
                        @component('cooperation.tool.components.step-question', [
                            'id' => 'yield-electricity',
                            'translation' => 'solar-panels.indication-for-costs.yield-electricity',
                            'required' => false, 'withInputSource' => false,
                        ])
                            <span class="input-group-prepend">kWh / @lang('general.unit.year.title')</span>
                            <input type="text" id="yield_electricity" class="form-input disabled"
                                   disabled="" value="0">
                        @endcomponent
                    </div>

                    <div class="w-full sm:w-1/3">
                        @component('cooperation.tool.components.step-question', [
                            'id' => 'raise-own-consumption',
                            'translation' => 'solar-panels.indication-for-costs.raise-own-consumption',
                            'required' => false, 'withInputSource' => false,
                        ])
                            <span class="input-group-prepend">%</span>
                            <input type="text" id="raise_own_consumption" class="form-input disabled"
                                   disabled="" value="0">
                        @endcomponent
                    </div>
                    <div class="w-full sm:w-1/3">
                        @include('cooperation.layouts.indication-for-costs.co2', [
                                'id' => null,
                                'translation' => 'solar-panels.index.costs.co2'
                        ])
                    </div>
                </div>
            </div>
            <div class="flex flex-row flex-wrap w-full sm:pad-x-6">
                <div class="w-full sm:w-1/3">
                    @include('cooperation.layouts.indication-for-costs.savings-in-euro',[
                            'id' => null,
                            'translation' => 'solar-panels.index.savings-in-euro'
                    ])
                </div>
                <div class="w-full sm:w-1/3">
                    @include('cooperation.layouts.indication-for-costs.indicative-costs',[
                        'id' => null,
                        'translation' => 'solar-panels.index.indicative-costs'
                    ])
                </div>
                <div class="w-full sm:w-1/3">
                    @include('cooperation.layouts.indication-for-costs.comparable-rent',[
                        'id' => null,
                        'translation' => 'solar-panels.index.comparable-rent'
                    ])
                </div>
            </div>
        </div>

        @include('cooperation.tool.includes.comment', [
             'translation' => 'solar-panels.index.specific-situation'
         ])

        @component('cooperation.tool.components.panel', [
            'label' => __('default.buttons.download'),
        ])
            <ol>
                <li><a download=""
                       href="{{asset('storage/hoomdossier-assets/Maatregelblad_Zonnepanelen.pdf')}}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Zonnepanelen.pdf')))))}}</a>
                </li>
            </ol>
        @endcomponent

        <input type="hidden" name="dirty_attributes" value="{{ old('dirty_attributes') }}">
    </form>
@endsection

@push('js')
    <script>
        $(document).ready(function () {
            let data = {};
            $('input:not(.source-select-input), textarea, select:not(.source-select)').change(function () {
                data[$(this).attr('name')] = $(this).val();
            });

            $('#solar-panels-form').submit(function () {
                $('input[name="dirty_attributes"]').val(JSON.stringify(data));
                return true;
            });

            $("select, input[type=radio], input[type=text]").change(() => formChange());

            function formChange() {
                var form = $('#solar-panels-form').serialize();
                $.ajax({
                    type: "POST",
                    url: '{{ route('cooperation.tool.solar-panels.calculate', compact('cooperation')) }}',
                    data: form,
                    success: function (data) {
                        if (data.hasOwnProperty('advice')) {
                            $("#solar-panels-advice").html("<strong>" + data.advice + "</strong>");
                            $(".advice").show();
                        } else {
                            $("#solar-panels-advice").html("");
                            $(".advice").hide();
                        }

                        if (data.hasOwnProperty('yield_electricity')) {
                            $("input#yield_electricity").val(hoomdossierRound(data.yield_electricity));
                        }
                        if (data.hasOwnProperty('raise_own_consumption')) {
                            $("input#raise_own_consumption").val(hoomdossierRound(data.raise_own_consumption));
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
                        if (data.hasOwnProperty('total_power')) {
                            $("#solar-panels-total-power").html(data.total_power);
                            $(".total-power").show();
                        } else {
                            $("#solar-panels-total-power").html("");
                            $(".total-power").hide();
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