@extends('cooperation.tool.layout')

@section('step_title', \App\Helpers\Translation::translate('high-efficiency-boiler.title.title'))


@section('step_content')
    <form class="form-horizontal" method="POST"
          action="{{ route('cooperation.tool.high-efficiency-boiler.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}
        @include('cooperation.tool.includes.interested', ['type' => 'service'])
        <div id="start-information">
            @include('cooperation.tool.includes.section-title', ['translation' => 'high-efficiency-boiler.title', 'id' => 'title',])
            <div class="row">
                <div class="col-sm-6">
                    @component('cooperation.tool.components.step-question', ['id' => 'habit.gas_usage', 'translation' => 'high-efficiency-boiler.current-gas-usage', 'required' => false])

                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'input', 'userInputValues' => $energyHabitsForMe, 'userInputColumn' => 'amount_gas'])
                            <span class="input-group-addon">m<sup>3</sup></span>
                            <input type="text" id="gas_usage" name="habit[gas_usage]" class="form-control"
                                   value="{{ old('habit.gas_usage', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'amount_gas', 0)) }}">
                            {{--<input type="text" id="gas_usage" name="habit[gas_usage]" class="form-control" value="{{ $habit instanceof \App\Models\UserEnergyHabit ? $habit->amount_gas : 0 }}">--}}
                        @endcomponent

                    @endcomponent

                </div>
                <div class="col-sm-6">
                    @component('cooperation.tool.components.step-question', ['id' => 'habit.resident_count', 'translation' => 'high-efficiency-boiler.resident-count', 'required' => false])
                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'input', 'userInputValues' => $energyHabitsForMe, 'userInputColumn' => 'resident_count'])
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input type="text" id="resident_count" name="habit[resident_count]" class="form-control"
                                   value="{{ old('habit.resident_count', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'resident_count', 0)) }}">
                            {{--<input type="text" id="resident_count" name="habit[resident_count]" class="form-control" value="{{ $habit instanceof \App\Models\UserEnergyHabit ? $habit->resident_count : 0 }}">--}}
                        @endcomponent

                    @endcomponent
                </div>
            </div>
        </div>
        <div id="intro-questions">
            <div class="row">
                <div id="boiler-options">
                    <div class="col-sm-6">
                        @component('cooperation.tool.components.step-question', ['id' => 'building_services.' . $boiler->id . '.service_value_id', 'translation' => 'high-efficiency-boiler.boiler-type', 'required' => false])

                            @component('cooperation.tool.components.input-group',
                            ['inputType' => 'select', 'inputValues' => $boilerTypes, 'userInputValues' => $installedBoilerForMe, 'userInputColumn' => 'service_value_id'])
                                <select id="high_efficiency_boiler_id" class="form-control"
                                        name="building_services[{{ $boiler->id  }}][service_value_id]">
                                    @foreach($boilerTypes as $boilerType)
                                        <option @if(old('building_services.' . $boiler->id . '.service_value_id', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingServices()->where('service_id', $boiler->id), 'service_value_id')) == $boilerType->id) selected="selected"
                                                @endif value="{{ $boilerType->id }}">{{ $boilerType->value }}</option>
                                        {{--<option @if(old('building_services.' . $boiler->id . '.service_value_id') == $boilerType->id || ($installedBoiler instanceof \App\Models\BuildingService && $installedBoiler->service_value_id == $boilerType->id)) selected @endif value="{{ $boilerType->id }}">{{ $boilerType->value }}</option>--}}
                                    @endforeach
                                </select>
                            @endcomponent

                        @endcomponent

                    </div>


                </div>
                <div class="col-sm-6">
                    @component('cooperation.tool.components.step-question', ['id' => 'building_services.' . $boiler->id . '.extra', 'translation' => 'high-efficiency-boiler.boiler-placed-date', 'required' => true])

                        <?php
                        $default = ($installedBoiler instanceof \App\Models\BuildingService && is_array($installedBoiler->extra) && array_key_exists('date', $installedBoiler->extra)) ? $installedBoiler->extra['date'] : '';
                        ?>

                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'input', 'userInputValues' => $installedBoilerForMe, 'userInputColumn' => 'extra.date'])
                            <input type="text" required class="form-control"
                                   value="{{ old('building_services.' . $boiler->id . '.extra', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingServices()->where('service_id', $boiler->id), 'extra.date')) }}"
                                   name="building_services[{{ $boiler->id }}][extra]">
                            {{--<input type="text" required class="form-control" value="{{ old('building_services.' . $boiler->id . '.extra', $default) }}" name="building_services[{{ $boiler->id }}][extra]">--}}
                        @endcomponent

                    @endcomponent


                </div>

                <div class="col-sm-12">
                    @component('cooperation.tool.components.step-question', ['id' => 'comment', 'translation' => 'general.specific-situation', 'required' => false])
                        <?php
                        $default = ($installedBoiler instanceof \App\Models\BuildingService && is_array($installedBoiler->extra) && array_key_exists('comment', $installedBoiler->extra)) ? $installedBoiler->extra['comment'] : '';
                        if (Auth::user()->hasRole('resident')) {
                            $default = ($installedBoiler instanceof \App\Models\BuildingService && is_array($installedBoiler->extra) && array_key_exists('comment', $installedBoiler->extra)) ? $installedBoiler->extra['comment'] : '';
                        } elseif (Auth::user()->hasRole('coach')) {
                            $coachInputSource = \App\Models\BuildingService::getCoachInput($installedBoilerForMe);

                            $default = ($coachInputSource instanceof \App\Models\BuildingService && is_array($coachInputSource->extra) && array_key_exists('comment', $coachInputSource->extra)) ? $coachInputSource->extra['comment'] : '';
                        }
                        ?>

                        <textarea name="comment" id=""
                                  class="form-control">{{old('comment', $default)}}</textarea>

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
                    @include('cooperation.layouts.indication-for-costs.gas', ['step' => 'high-efficiency-boiler'])
                </div>
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.co2', ['step' => 'high-efficiency-boiler'])
                </div>
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.savings-in-euro')
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group add-space">
                        @component('cooperation.tool.components.step-question', ['id' => 'indicative-replacement', 'translation' => 'high-efficiency-boiler.indication-for-costs.indicative-replacement', 'required' => false])
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                <input type="text" id="replace_year" class="form-control disabled" disabled="">
                            </div>
                        @endcomponent
                    </div>
                </div>
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.indicative-costs')
                </div>
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.comparable-rent')
                </div>
            </div>
        </div>

        @include('cooperation.tool.includes.comment', [
             'collection' => $installedBoilerForMe,
             'commentColumn' => 'extra.comment',
             'translation' => [
                 'title' => 'general.specific-situation.title',
                 'help' => 'general.specific-situation.help'
            ]
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
                <hr>
                @if(!\App\helpers\HoomdossierSession::isUserObserving())
                <div class="form-group add-space">
                    <div class="">
                        <a class="btn btn-success pull-left"
                           href="{{ route('cooperation.tool.roof-insulation.index', ['cooperation' => $cooperation]) }}">@lang('default.buttons.prev')</a>
                        <button type="submit" class="btn btn-primary pull-right">
                            @lang('default.buttons.next')
                        </button>
                    </div>
                </div>
                    @endif
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
                            $("input#savings_gas").val(Math.round(data.savings_gas));
                        }
                        if (data.hasOwnProperty('savings_co2')) {
                            $("input#savings_co2").val(Math.round(data.savings_co2));
                        }
                        if (data.hasOwnProperty('savings_money')) {
                            $("input#savings_money").val(Math.round(data.savings_money));
                        }
                        if (data.hasOwnProperty('cost_indication')) {
                            $("input#cost_indication").val(Math.round(data.cost_indication));
                        }
                        if (data.hasOwnProperty('replace_year')) {
                            $("input#replace_year").val(data.replace_year);
                        }
                        if (data.hasOwnProperty('interest_comparable')) {
                            $("input#interest_comparable").val(data.interest_comparable);
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
