@extends('cooperation.tool.layout')

@section('step_title', __('woningdossier.cooperation.tool.my-plan.title'))

@section('page_class', 'page-my-plan')

@section('step_content')

    <div class="row">
        <div class="col-md-12">
            {{--<h1>@lang('woningdossier.cooperation.tool.my-plan.title')</h1>--}}
            <p>@lang('woningdossier.cooperation.tool.my-plan.description')</p>
        </div>
    </div>


    <form class="form-horizontal" action="{{ route('cooperation.tool.my-plan.store', ['cooperation' => $cooperation]) }}" method="post">
        {{ csrf_field() }}
    @foreach($advices as $measureType => $stepAdvices)
        <div class="row">
            <div class="col-md-12">
                <h2>@if($measureType == 'energy_saving') @lang('woningdossier.cooperation.tool.my-plan.energy-saving-measures') @else @lang('woningdossier.cooperation.tool.my-plan.maintenance-measures') @endif</h2>
            </div>

            <div class="col-md-12">
                <table class="table table-condensed table-responsive">
                    <thead>
                    <tr>
                        <th>@lang('woningdossier.cooperation.tool.my-plan.columns.interest')</th>
                        <th>@lang('woningdossier.cooperation.tool.my-plan.columns.measure')</th>
                        <th>@lang('woningdossier.cooperation.tool.my-plan.columns.costs')</th>
                        <th>@lang('woningdossier.cooperation.tool.my-plan.columns.savings-gas')</th>
                        <th>@lang('woningdossier.cooperation.tool.my-plan.columns.savings-electricity')</th>
                        <th>@lang('woningdossier.cooperation.tool.my-plan.columns.savings-costs')</th>
                        <th>@lang('woningdossier.cooperation.tool.my-plan.columns.advice-year')</th>
                        <th>@lang('woningdossier.cooperation.tool.my-plan.columns.planned-year')</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($stepAdvices as $stepSlug => $advicesForStep)
                        @foreach($advicesForStep as $advice)
                            <?php $step = \App\Models\Step::where('slug', $stepSlug)->first() ?>
                            <tr>
                                <td>
                                    <input name="interested" value="1" type="checkbox" id="advice-{{$advice->id}}-planned"
                                           @if(\App\Helpers\MyPlanHelper::isUserInterestedInMeasure($step))
                                                   checked
                                           @endif
                                    />
                                </td>
                                <td>
                                    {{ $advice->measureApplication->measure_name }}
                                </td>
                                <td>
                                    &euro; {{ \App\Helpers\NumberFormatter::format($advice->costs) }}
                                </td>
                                <td>
                                    {{ \App\Helpers\NumberFormatter::format($advice->savings_gas) }} m<sup>3</sup>
                                </td>
                                <td>
                                    {{ \App\Helpers\NumberFormatter::format($advice->savings_electricity) }} kWh
                                </td>
                                <td>
                                    &euro; {{ \App\Helpers\NumberFormatter::format($advice->savings_money) }}
                                </td>
                                <td>
                                    {{ $advice->year }}
                                </td>
                                <td>
                                    <input type="text" maxlength="4" size="4" class="form-control" name="advice[{{ $advice->id }}][{{$stepSlug}}][planned_year]" value="{{ $advice->planned_year }}" />
                                </td>
                            </tr>
                        @endforeach
{{--
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ $step }}</div>
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-md-1">
                                <strong>@lang('woningdossier.cooperation.tool.my-plan.columns.interest')</strong>
                            </div>
                            <div class="col-md-2">
                                <strong>@lang('woningdossier.cooperation.tool.my-plan.columns.measure')</strong>
                            </div>
                            <div class="col-md-1">
                                <strong>@lang('woningdossier.cooperation.tool.my-plan.columns.costs')</strong>
                            </div>
                            <div class="col-md-2">
                                <strong>@lang('woningdossier.cooperation.tool.my-plan.columns.savings-gas')</strong>
                            </div>
                            <div class="col-md-2">
                                <strong>@lang('woningdossier.cooperation.tool.my-plan.columns.savings-electricity')</strong>
                            </div>
                            <div class="col-md-2">
                                <strong>@lang('woningdossier.cooperation.tool.my-plan.columns.savings-costs')</strong>
                            </div>
                            <div class="col-md-1">
                                <strong>@lang('woningdossier.cooperation.tool.my-plan.columns.advice-year')</strong>
                            </div>
                            <div class="col-md-1">
                                <strong>@lang('woningdossier.cooperation.tool.my-plan.columns.planned-year')</strong>
                            </div>

                        </div>

                        @foreach($advicesForStep as $advice)
                        <div class="row">
                            <div class="col-md-1">
                                <input type="checkbox" @if($advice->planned)checked="checked"@endif name="advice[{{ $advice->id }}][planned]" />
                            </div>
                            <div class="col-md-2">
                                {{ $advice->measureApplication->measure_name }}
                            </div>
                            <div class="col-md-1">
                                {{ \App\Helpers\NumberFormatter::format($advice->costs) }}
                            </div>
                            <div class="col-md-2">
                                {{ \App\Helpers\NumberFormatter::format($advice->savings_gas) }}
                            </div>
                            <div class="col-md-2">
                                {{ \App\Helpers\NumberFormatter::format($advice->savings_electricity) }}
                            </div>
                            <div class="col-md-2">
                                {{ \App\Helpers\NumberFormatter::format($advice->savings_money) }}
                            </div>
                            <div class="col-md-1">
                                {{ $advice->year }}
                            </div>
                            <div class="col-md-1">
                                <input type="text" maxlength="4" size="4" class="form-control" name="advice[{{ $advice->id }}][planned_year]" value="{{ $advice->planned_year }}" />
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            --}}
            @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    @endforeach
    </form>


    <div class="row">
        <div class="plan-preview col-md-12">
            <h2>@lang('woningdossier.cooperation.tool.my-plan.maintenance-plan')</h2>
            <ul id="years">

            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">@lang('default.buttons.download')</div>
                <div class="panel-body">
                    <ol>
                        <li><a download="" href="{{ asset('storage/hoomdossier-assets/Invul_hulp_Actieplan.pdf') }}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Invul_hulp_Actieplan.pdf')))))}}</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <a href="{{ route('cooperation.tool.my-plan.export', ['cooperation' => $cooperation]) }}"  class="pull-right btn btn-primary">@lang('woningdossier.cooperation.tool.my-plan.download')</a>
            </div>
        </div>
    </div>
@endsection


@push('js')
    <script>
        $(document).ready(function(){
            $(window).keydown(function(event){
                if(event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });

            $("select, input[type=radio], input[type=text], input[type=checkbox]").change(function(){


                var form = $(this).closest("form").serialize();
                $.ajax({
                    type: "POST",
                    url: '{{ route('cooperation.tool.my-plan.store', [ 'cooperation' => $cooperation ]) }}',
                    data: form,
                    success: function(data){
                        $("ul#years").html("");
                        $.each(data, function(year, steps){
                            var header = "<h1>" + year + "</h1>";
                            var table = "<table class=\"table table-condensed table-responsive table-striped\"><thead><tr><th>@lang('woningdossier.cooperation.tool.my-plan.columns.measure')</th><th>@lang('woningdossier.cooperation.tool.my-plan.columns.costs')</th><th>@lang('woningdossier.cooperation.tool.my-plan.columns.savings-gas')</th><th>@lang('woningdossier.cooperation.tool.my-plan.columns.savings-electricity')</th><th>@lang('woningdossier.cooperation.tool.my-plan.columns.savings-costs')</th></tr></thead><tbody>";

                            var totalCosts = 0;
                            var totalSavingsGas = 0;
                            var totalSavingsElectricity = 0;
                            var totalSavingsMoney = 0;

                            $.each(steps, function(stepName, stepMeasures){

                                $.each(stepMeasures, function(i, stepData){

                                    if (stepData.interested) {
                                        $("#advice-"+stepData.advice_id+"-planned").attr('checked', true)
                                    }

                                    totalCosts += parseFloat(stepData.costs);
                                    totalSavingsGas += parseFloat(stepData.savings_gas);
                                    totalSavingsElectricity += parseFloat(stepData.savings_electricity);
                                    totalSavingsMoney += parseFloat(stepData.savings_money);

                                    table += "<tr><td>" + stepData.measure + "</td><td>&euro; " + Math.round(stepData.costs).toLocaleString('{{ app()->getLocale() }}') + "</td><td>" + Math.round(stepData.savings_gas).toLocaleString('{{ app()->getLocale() }}') + " m<sup>3</sup></td><td>" + Math.round(stepData.savings_electricity).toLocaleString('{{ app()->getLocale() }}') + " kWh</td><td>&euro; " + Math.round(stepData.savings_money).toLocaleString('{{ app()->getLocale() }}') + "</td></tr>";
                                });

                            });

                            table += "<tr><td><strong>Totaal</strong></td><td><strong>&euro; " + Math.round(totalCosts).toLocaleString('{{ app()->getLocale() }}') + "</strong></td><td><strong>" + Math.round(totalSavingsGas).toLocaleString('{{ app()->getLocale() }}') + " m<sup>3</sup></strong></td><td><strong>" + Math.round(totalSavingsElectricity).toLocaleString('{{ app()->getLocale() }}') + " kWh</strong></td><td><strong>&euro; " + Math.round(totalSavingsMoney).toLocaleString('{{ app()->getLocale() }}') + "</strong></td></tr>";
                            table += "<tr><td colspan=\"5\"></td></tr>";

                            table += "</tbody></table>";

                            $("ul#years").append("<li>" + header + table + "</li>");
                        });



                        @if(App::environment('local'))
                        console.log(data);
                        @endif
                    }
                })
            });
            // Trigger the change event so it will load the data
            $('form').find('*').filter(':input:visible:first').trigger('change');
        });


    </script>
@endpush

