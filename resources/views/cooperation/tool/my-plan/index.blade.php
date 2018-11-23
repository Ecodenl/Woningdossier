@extends('cooperation.tool.layout')

@section('step_title', __('woningdossier.cooperation.tool.my-plan.title'))

@section('page_class', 'page-my-plan')

@section('step_content')

    <div class="row">
        <div class="col-md-12">
            {{--<h1>@lang('woningdossier.cooperation.tool.my-plan.title')</h1>--}}
            <p>@lang('woningdossier.cooperation.tool.my-plan.description')</p>
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#messagesModal">@lang('woningdossier.cooperation.tool.my-plan.coach-comments.title')</button>
        </div>
    </div>



    @component('cooperation.tool.components.modal', ['id' => 'messagesModal'])
        @slot('title')
            @lang('woningdossier.cooperation.tool.my-plan.coach-comments.title')
        @endslot

        @foreach($coachComments as $stepName => $coachComment)
            <h4>@lang('woningdossier.cooperation.tool.my-plan.coach-comments.'.$stepName)</h4>
            <p>{{$coachComment}}</p>
            <hr>
        @endforeach
    @endcomponent

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
                        <th style="width: 8%">@lang('woningdossier.cooperation.tool.my-plan.columns.more-info')</th>
                        <th style="width: 5%">@lang('woningdossier.cooperation.tool.my-plan.columns.interest')</th>
                        <th style="width: 45%">@lang('woningdossier.cooperation.tool.my-plan.columns.measure')</th>
                        <th style="width: 12%">@lang('woningdossier.cooperation.tool.my-plan.columns.costs')</th>
                        <th style="width: 12%">@lang('woningdossier.cooperation.tool.my-plan.columns.savings-costs')</th>
                        <th style="width: 9%">@lang('woningdossier.cooperation.tool.my-plan.columns.advice-year')</th>
                        <th style="width: 9%">@lang('woningdossier.cooperation.tool.my-plan.columns.planned-year')</th>
                    </tr>
                    </thead>
                <tbody>

            @foreach($stepAdvices as $stepSlug => $advicesForStep)
                @foreach($advicesForStep as $advice)
	                <?php $step = \App\Models\Step::where('slug', $stepSlug)->first() ?>
                    <tr>
                        <input type="hidden" name="advice[{{ $advice->id }}][{{$stepSlug}}][measure_type]" value="{{$measureType}}">
                        <td >
                            <a type="#" data-toggle="collapse" data-target="#more-info-{{$advice->id}}"> <i class="glyphicon glyphicon-chevron-down"></i> </a>
                        </td>

                        <td>
                            @if($measureType == "energy_saving")
                                <input class="interested-checker" name="advice[{{ $advice->id }}][{{$stepSlug}}][interested]" value="1" type="checkbox" id="advice-{{$advice->id}}-planned" @if(\App\Helpers\StepHelper::hasInterestInStep($step) && $advice->planned) checked @endif />
                            @else
                                <input class="interested-checker" name="advice[{{ $advice->id }}][{{$stepSlug}}][interested]" value="1" type="checkbox" id="advice-{{$advice->id}}-planned" @if($advice->planned) checked @endif />
                            @endif
                        </td>
                        <td>
                            {{ $advice->measureApplication->measure_name }}
                        </td>
                        <td>
                            &euro; {{ \App\Helpers\NumberFormatter::format($advice->costs) }}
                        </td>
                        <td>
                            &euro; {{ \App\Helpers\NumberFormatter::format($advice->savings_money) }}
                        </td>
                        <td class="advice-year">
                            {{ $advice->year }}
                        </td>
                        <td>
                            <input type="text" maxlength="4" size="4" class="form-control" name="advice[{{ $advice->id }}][{{ $stepSlug }}][planned_year]" value="{{ $advice->planned_year }}" />
                        </td>
                    </tr>
                    <tr class="collapse" id="more-info-{{$advice->id}}" >
                        <td colspan="2"></td>
                        <td colspan="">
                            <strong>@lang('woningdossier.cooperation.tool.my-plan.columns.savings-gas'):</strong>
                            <br>
                            <strong>@lang('woningdossier.cooperation.tool.my-plan.columns.savings-electricity'):</strong>
                        </td>
                        <td>
                            {{ \App\Helpers\NumberFormatter::format($advice->savings_gas) }} m<sup>3</sup>
                            <br>
                            {{ \App\Helpers\NumberFormatter::format($advice->savings_electricity) }} kWh
                        </td>
                        <td colspan="3">
                        </td>
                    </tr>
                @endforeach
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

                            var slugYear = year.replace( /\s+/g, '');

                            var table = "<table class=\"table table-condensed table-responsive\"> <thead> <tr> <th style=\"width: 8%\">@lang('woningdossier.cooperation.tool.my-plan.columns.more-info')</th> <th style=\"width: 62%\">@lang('woningdossier.cooperation.tool.my-plan.columns.measure')</th> <th style=\"width: 15%\">@lang('woningdossier.cooperation.tool.my-plan.columns.costs')</th> <th style=\"width: 15%\">@lang('woningdossier.cooperation.tool.my-plan.columns.savings-costs')</th> </tr></thead> <tbody>";

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

                                    var slug = stepName.replace( /\s+/g, '');

                                    table += "<tr> <td> <a type=\"#\" class='turn-on-click' data-toggle=\"collapse\" data-target=\"#more-personal-plan-info-" + slug + "-" + i + "-" + slugYear + "\"> <i class=\"glyphicon glyphicon-chevron-down\"></i> </a> </td><td>" + stepData.measure + "</td><td>&euro; " + Math.round(stepData.costs).toLocaleString('{{ app()->getLocale() }}') + "</td><td>&euro; " + Math.round(stepData.savings_money).toLocaleString('{{ app()->getLocale() }}') + "</td></tr>";
                                    table += " <tr class='collapse' id='more-personal-plan-info-" + slug + "-" + i + "-" + slugYear + "' > <td colspan='1'></td><td colspan=''> <strong>@lang('woningdossier.cooperation.tool.my-plan.columns.savings-gas'):</strong> <br><strong>@lang('woningdossier.cooperation.tool.my-plan.columns.savings-electricity'):</strong> </td><td>"+ Math.round(stepData.savings_gas).toLocaleString('{{ app()->getLocale() }}') +" m<sup>3</sup> <br>"+Math.round(stepData.savings_electricity).toLocaleString('{{ app()->getLocale() }}')+" kWh </td><td colspan='1'> </td></tr>"
                                });

                            });

                            // total calculation
                            table += "<tr><td><a type='#' class='turn-on-click' data-toggle='collapse' data-target='#total-costs-" + slugYear + "-total'> <i class=\"glyphicon glyphicon-chevron-down\"></i> </a> </td><td><strong>Totaal</strong></td><td><strong>&euro; " + Math.round(totalCosts).toLocaleString('{{ app()->getLocale() }}') + "</strong></td><td><strong>&euro; " + Math.round(totalSavingsMoney).toLocaleString('{{ app()->getLocale() }}') + "</strong></td></tr>";
                            table += "<tr class='collapse' id='total-costs-" + slugYear + "-total' > <td colspan='1'></td><td colspan=''> <strong>@lang('woningdossier.cooperation.tool.my-plan.columns.savings-gas'):</strong> <br><strong>@lang('woningdossier.cooperation.tool.my-plan.columns.savings-electricity'):</strong> </td><td>"+Math.round(totalSavingsGas).toLocaleString('{{ app()->getLocale() }}')+" m<sup>3</sup> <br>"+Math.round(totalSavingsElectricity).toLocaleString('{{ app()->getLocale() }}')+" kWh </td><td colspan='1'> </td></tr>";


                            table += "</tbody></table>";

                            $("ul#years").append("<li>" + header + table + "</li>");
                        });

                        // toggle cheveron for the personal plan
                        $('.turn-on-click').on('click', function () {
                            $(this).toggleClass('clicked');

                            if ($(this).hasClass('clicked')) {
                                $(this).find('i').css("transform", "rotate(-180deg)");
                                $(this).find('i').css("transition", "1s");
                            } else {
                                $(this).find('i').css("transform", "rotate(0deg)");
                                $(this).find('i').css("transition", "1s");
                            }
                        });

                        @if(App::environment('local'))
                            console.log(data);
                        @endif
                    }
                });

            });
            // Trigger the change event so it will load the data
            $('form').find('*').filter(':input:visible:first').trigger('change');

            // Toggle chevron op open / close
            $('a[data-target*=more]').on('click', function () {
                $(this).toggleClass('clicked');

                if ($(this).hasClass('clicked')) {
                    $(this).find('i').css("transform", "rotate(-180deg)");
                    $(this).find('i').css("transition", "1s");
                } else {
                    $(this).find('i').css("transform", "rotate(0deg)");
                    $(this).find('i').css("transition", "1s");
                }
            });

            // if a user clicks the interested check box
            $('.interested-checker').on('click', function() {

                // get the planned year input
                var plannedYearInput = $(this).parent().parent().find('input[name*=planned_year]');
                // check if the checkbox is checked
                // if so, so fill the
                if ($(this).is(':checked')) {
                    var advicedYear = $(this).parent().parent().find('.advice-year').html().trim();

                    if(advicedYear === "") {
                        advicedYear = (new Date()).getFullYear();
                    }

                    plannedYearInput.val(advicedYear);
                } else {
                    plannedYearInput.val("");
                }
            });

        });
    </script>
@endpush

