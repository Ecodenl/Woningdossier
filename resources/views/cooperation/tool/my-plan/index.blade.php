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
                        <input type="hidden" class="measure_short" value="{{$advice->measureApplication->short}}">
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
                            {{--<a href="#myModal" role="button" class="btn btn-large btn-primary" data-toggle="modal">Launch Demo Modal</a>--}}
                            {{ $advice->measureApplication->measure_name }} <a href="#warning-modal" role="button" class="measure-warning" data-toggle="modal" style="display:none;"><i class="glyphicon glyphicon-warning-sign" role="button" data-toggle="modal" title="" style="color: #ffc107"></i></a>
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
                            <input type="text" maxlength="4" size="4" class="form-control planned-year" name="advice[{{ $advice->id }}][{{ $stepSlug }}][planned_year]" value="{{ $advice->planned_year }}" />
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


    <div id="warning-modal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">@lang('woningdossier.cooperation.tool.my-plan.warnings.title')</h4>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('js')
    <script>
        $(document).ready(function(){
            const ROOF_INSULATION_FLAT_REPLACE_CURRENT = "roof-insulation-flat-replace-current";
            const REPLACE_ROOF_INSULATION = "replace-roof-insulation";

            const ROOF_INSULATION_PITCHED_REPLACE_TILES = "roof-insulation-pitched-replace-tiles";
            const REPLACE_TILES = "replace-tiles";

            $(window).keydown(function(event){
                if(event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });

            $("select, input[type=radio], input[type=text], input[type=checkbox]").change(function() {
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

                        // toggle chevron for the personal plan
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

                        checkCoupledMeasuresAndMaintenance();
                    }
                });

            });
            // Trigger the change event so it will load the data
            $('form').find('*').filter(':input:visible:first').trigger('change');

            $('#warning-modal').on('shown.bs.modal', function (e) {
                var clicked = $(e.relatedTarget);
                var icon = clicked.find('i.glyphicon');
                $(this).find('.modal-body').html('<p>' + icon.attr('title') + '</p>');
            });

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

            $(".interested-checker").click(function(){
                // get the planned year input
                var plannedYearInput = $(this).parent().parent().find('input[name*=planned_year]');
                var advicedYear;
                var measureApplicationShort = $(this).parent().parent().find('.measure_short').val();

                if (getPlanned(measureApplicationShort)) {
                    advicedYear = getPlannedYear(measureApplicationShort);
                    plannedYearInput.val(advicedYear)
                } else {
                    plannedYearInput.val("");
                }
            });

            function checkCoupledMeasuresAndMaintenance() {
                // remove all previous warnings and recheck
                removeWarnings();

                // flat roof
                if (getPlanned(ROOF_INSULATION_FLAT_REPLACE_CURRENT)) {
                    if (!getPlanned(REPLACE_ROOF_INSULATION)) {
                        // set warning
                        setWarning(ROOF_INSULATION_FLAT_REPLACE_CURRENT, '@lang('woningdossier.cooperation.tool.my-plan.warnings.check-order')');
                        setWarning(REPLACE_ROOF_INSULATION, '@lang('woningdossier.cooperation.tool.my-plan.warnings.check-order')');
                    }
                    else {
                        // both were planned
                        if (getPlannedYear(ROOF_INSULATION_FLAT_REPLACE_CURRENT) !== getPlannedYear(REPLACE_ROOF_INSULATION)) {
                            // set warning
                            setWarning(ROOF_INSULATION_FLAT_REPLACE_CURRENT, '@lang('woningdossier.cooperation.tool.my-plan.warnings.planned-year')');
                            setWarning(REPLACE_ROOF_INSULATION, '@lang('woningdossier.cooperation.tool.my-plan.warnings.planned-year')');
                        }
                    }
                }

                // pitched roof
                if (getPlanned(ROOF_INSULATION_PITCHED_REPLACE_TILES)) {
                    if (!getPlanned(REPLACE_TILES)) {
                        // set warning
                        setWarning(ROOF_INSULATION_PITCHED_REPLACE_TILES, '@lang('woningdossier.cooperation.tool.my-plan.warnings.check-order')');
                        setWarning(REPLACE_TILES, '@lang('woningdossier.cooperation.tool.my-plan.warnings.check-order')');
                    }
                    else {
                        // both were planned
                        if (getPlannedYear(ROOF_INSULATION_PITCHED_REPLACE_TILES) !== getPlannedYear(REPLACE_TILES)) {
                            // set warning
                            setWarning(ROOF_INSULATION_PITCHED_REPLACE_TILES, '@lang('woningdossier.cooperation.tool.my-plan.warnings.planned-year')');
                            setWarning(REPLACE_TILES, '@lang('woningdossier.cooperation.tool.my-plan.warnings.planned-year')');
                        }
                    }
                }
            }

            // Return if the measure is planned (checked) or not.
            function getPlanned(maShort){
                var row = getMeasureRow(maShort);
                if (row !== null){
                    return row.find('input.interested-checker').is(':checked');
                }
                return false;
            }

            // Returns the planned year for the measure. Either the user-defined
            // year or advised year, if both are not set we get the current year.
            function getPlannedYear(maShort){
                var row = getMeasureRow(maShort);
                if (row !== null){
                    var planned = row.find('input.planned-year').val();
                    if (planned === ''){
                        planned = parseInt(row.find('.advice-year').text().trim());
                    }
                    else {
                        planned = parseInt(planned);
                    }
                    // if the row has no adviced year it will still be empty, so set it to the current year.
                    if (!Number.isInteger(planned)) {
                        planned = (new Date()).getFullYear();
                    }
                    return planned;
                }
                return null;
            }

            // Returns the <tr> (holding) element for a particular measure
            function getMeasureRow(maShort){
                var element = $("input.measure_short[value=" + maShort + "]");
                if (element.length){
                    return element.parent();
                }
                return null;
            }

            // Set a warning for a measure application
            function setWarning(maShort, warning){
                var row = getMeasureRow(maShort);
                var link = row.find('a.measure-warning');
                var icon = link.find('i');
                icon.attr('title', warning);
                link.show();
            }

            function removeWarnings(){
                $("a.measure-warning").hide();
            }

        });
    </script>
@endpush

