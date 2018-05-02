@extends('cooperation.tool.layout')

@section('step_title', __('woningdossier.cooperation.tool.boiler.title'))


@section('step_content')
    <form class="form-horizontal" method="POST" action="{{ route('cooperation.tool.high-efficiency-boiler.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}
        <div id="start-information">
            <h4 style="margin-left: -5px">@lang('woningdossier.cooperation.tool.boiler.title')</h4>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group add-space {{ $errors->has('habit.gas_usage') ? ' has-error' : '' }}">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.boiler.current-gas-usage')</label>
                        <div class="input-group">
                            <span class="input-group-addon">m<sup>3</sup></span>
                            <input type="text" id="gas_usage" name="habit[gas_usage]" class="form-control" value="{{ $habit instanceof \App\Models\UserEnergyHabit ? $habit->amount_gas : 0 }}">
                        </div>

                        @if ($errors->has('habit.gas_usage'))
                            <span class="help-block">
                                    <strong>{{ $errors->first('habit.gas_usage') }}</strong>
                                </span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group add-space {{ $errors->has('habit.resident_count') ? ' has-error' : '' }}">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.boiler.resident-count')</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input type="text" id="resident_count" name="habit[resident_count]" class="form-control" value="{{ $habit instanceof \App\Models\UserEnergyHabit ? $habit->resident_count : 0 }}">
                        </div>

                        @if ($errors->has('habit.resident_count'))
                            <span class="help-block">
                                    <strong>{{ $errors->first('habit.resident_count') }}</strong>
                                </span>
                        @endif

                    </div>
                </div>
            </div>
        </div>
        <div id="intro-questions">
            <div class="row">
                <div id="boiler-options" >
                    <div class="col-sm-6">
                        <div class="form-group add-space{{ $errors->has('building_services.' . $boiler->id . '.service_value_id') ? ' has-error' : '' }}">
                            <label for="high_efficiency_boiler_id" class=" control-label"><i data-toggle="collapse" data-target="#high-efficiency-boiler-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('woningdossier.cooperation.tool.boiler.boiler-type') </label>

                            <select id="high_efficiency_boiler_id" class="form-control" name="building_services[{{ $boiler->id  }}][service_value_id]">
                                @foreach($boilerTypes as $boilerType)
                                    <option @if(old('building_services.' . $boiler->id . '.service_value_id') == $boilerType->id || ($installedBoiler instanceof \App\Models\BuildingService && $installedBoiler->service_value_id == $boilerType->id)) selected @endif value="{{ $boilerType->id }}">{{ $boilerType->value }}</option>
                                @endforeach
                            </select>

                            <div id="high-efficiency-boiler-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And i would like to have it to...
                            </div>

                            @if ($errors->has('building_services.' . $boiler->id . '.service_value_id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('building_services.' . $boiler->id . '.service_value_id') }}</strong>
                                </span>
                            @endif
                        </div>



                    </div>
                    <div class="col-sm-6">
                        <div class="form-group add-space{{ $errors->has('building_services.' . $boiler->id . '.extra') ? ' has-error' : '' }}">
                            <label for="high_efficiency_boiler_placed_date" class=" control-label">
                                <i data-toggle="collapse" data-target="#high-efficiency-boiler-placed-date-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                @lang('woningdossier.cooperation.tool.boiler.boiler-placed-date')
                            </label> <span> *</span>

                            <?php
                                $default = ($installedBoiler instanceof \App\Models\BuildingService && is_array($installedBoiler->extra) && array_key_exists('date', $installedBoiler->extra)) ? $installedBoiler->extra['date'] : '';
                            ?>

                            <input type="text" required class="form-control" value="{{ old('building_services.' . $boiler->id . '.extra', $default) }}" name="building_services[{{ $boiler->id }}][extra]">

                            <div id="high-efficiency-boiler-placed-date-info" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And i would like to have it to...
                            </div>

                            @if ($errors->has('building_services.' . $boiler->id . '.extra'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('building_services.' . $boiler->id . '.extra') }}</strong>
                                </span>
                            @endif
                        </div>


                    </div>
                    
                    <div class="col-sm-12">
                        <div class="form-group add-space{{ $errors->has('comment') ? ' has-error' : '' }}">
                            <label for="" class=" control-label"><i data-toggle="collapse" data-target="#comment" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('default.form.input.comment') </label>
                            <?php
                                $default = ($installedBoiler instanceof \App\Models\BuildingService && is_array($installedBoiler->extra) && array_key_exists('comment', $installedBoiler->extra)) ? $installedBoiler->extra['comment'] : '';
                            ?>

                            <textarea name="comment" id="" class="form-control">{{old('comment', $default)}}</textarea>

                            <div id="comment" class="collapse alert alert-info remove-collapse-space alert-top-space">
                                And i would like to have it to...
                            </div>

                            @if ($errors->has('comment'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('comment') }}</strong>
                                </span>
                            @endif
                        </div>
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
        </div>
        <div id="indication-for-costs">
            <hr>
            <h4 style="margin-left: -5px">@lang('woningdossier.cooperation.tool.boiler.indication-for-costs.title')</h4>

            <div id="costs" class="row">
                <div class="col-sm-4">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.boiler.indication-for-costs.gas-savings')</label>
                        <div class="input-group">
                            <span class="input-group-addon">m<sup>3</sup> / @lang('woningdossier.cooperation.tool.boiler.indication-for-costs.year')</span>
                            <input type="text" id="savings_gas" class="form-control disabled" disabled="" value="0">
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.boiler.indication-for-costs.co2-savings')</label>
                        <div class="input-group">
                            <span class="input-group-addon">CO<sub>2</sub> / @lang('woningdossier.cooperation.tool.boiler.indication-for-costs.year')</span>
                            <input type="text" id="savings_co2" class="form-control disabled" disabled="" value="0">
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.boiler.indication-for-costs.savings-in-euro')</label>
                        <div class="input-group">
                            <span class="input-group-addon">€ / @lang('woningdossier.cooperation.tool.boiler.indication-for-costs.year')</span>
                            <input type="text" id="savings_money" class="form-control disabled" disabled="" value="0">
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.boiler.indication-for-costs.indicative-replacement')</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                            <input type="text" id="replace_year" class="form-control disabled" disabled="" >
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.boiler.indication-for-costs.indicative-costs')</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                            <input type="text" id="cost_indication" class="form-control disabled" disabled="" value="0">
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group add-space">
                        <label class="control-label">@lang('woningdossier.cooperation.tool.boiler.indication-for-costs.comparable-rate')</label>
                        <div class="input-group">
                            <span class="input-group-addon">% / @lang('woningdossier.cooperation.tool.boiler.indication-for-costs.year')</span>
                            <input type="text" id="interest_comparable" class="form-control disabled" disabled="" value="0,0">
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class="row">
            <div class="col-md-12">
                <hr>
                <div class="form-group add-space">
                    <div class="">
                        <a class="btn btn-success pull-left" href="{{ route('cooperation.tool.roof-insulation.index', ['cooperation' => $cooperation]) }}">@lang('default.buttons.prev')</a>
                        <button type="submit" class="btn btn-primary pull-right">
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
        $(document).ready(function() {

            $(window).keydown(function(event){
                if(event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });

            $("select, input[type=radio], input[type=text]").change(formChange);

            function formChange(){
                var form = $(this).closest("form").serialize();
                $.ajax({
                    type: "POST",
                    url: '{{ route('cooperation.tool.high-efficiency-boiler.calculate', [ 'cooperation' => $cooperation ]) }}',
                    data: form,
                    success: function(data){
                        if (data.boiler_advice){
                            $("#boiler-advice").html("<strong>" + data.boiler_advice + "</strong>");
                            $(".advice").show();
                        }
                        else {
                            $("#boiler-advice").html("");
                            $(".advice").hide();
                        }

                        if (data.hasOwnProperty('savings_gas')){
                            $("input#savings_gas").val(Math.round(data.savings_gas));
                        }
                        if (data.hasOwnProperty('savings_co2')){
                            $("input#savings_co2").val(Math.round(data.savings_co2));
                        }
                        if (data.hasOwnProperty('savings_money')){
                            $("input#savings_money").val(Math.round(data.savings_money));
                        }
                        if (data.hasOwnProperty('cost_indication')){
                            $("input#cost_indication").val(Math.round(data.cost_indication));
                        }
                        if (data.hasOwnProperty('replace_year')){
                            $("input#replace_year").val(data.replace_year);
                        }
                        if (data.hasOwnProperty('interest_comparable')){
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
