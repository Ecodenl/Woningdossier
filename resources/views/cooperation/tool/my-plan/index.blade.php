@extends('cooperation.tool.layout')

@section('step_title', \App\Helpers\Translation::translate('my-plan.title.title'))

@push('meta')
    @if($anyFilesBeingProcessed)
        <meta http-equiv="refresh" content="5">
    @endif
@endpush


@section('page_class', 'page-my-plan')

@section('step_content')

    @if(!\App\Helpers\HoomdossierSession::isUserObserving())
        <div class="row">
            <div class="col-md-12">
                <p>{!! \App\Helpers\Translation::translate('my-plan.description.title') !!}</p>
                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#messagesModal">{{ \App\Helpers\Translation::translate('my-plan.coach-comments.title') }}</button>
            </div>
        </div>

        @component('cooperation.tool.components.modal', ['id' => 'messagesModal'])
            @slot('title')
                {{ \App\Helpers\Translation::translate('my-plan.coach-comments.title') }}
            @endslot

            @foreach($coachCommentsByStep as $stepName => $coachComments)
                <h4>@lang('woningdossier.cooperation.tool.my-plan.coach-comments.'.$stepName)</h4>
                @foreach($coachComments as $coachComment)
                    <p>{{$coachComment}}</p>
                    <hr>
                @endforeach
            @endforeach
        @endcomponent
    @endif

    <form class="form-horizontal" action="{{ route('cooperation.tool.my-plan.store', ['cooperation' => $cooperation]) }}" method="post">
        {{ csrf_field() }}
    @foreach($advices as $measureType => $stepAdvices)
        <div class="row">

            <div class="col-md-12">
                <h2>@if($measureType == 'energy_saving') {{ \App\Helpers\Translation::translate('my-plan.energy-saving-measures.title') }} @else {{ \App\Helpers\Translation::translate('my-plan.maintenance-measures.title') }} @endif</h2>
            </div>


            <div class="col-md-12">
                <table class="table table-condensed table-responsive">
                    <thead>
                    <tr>
                        <th style="width: 8%">{{ \App\Helpers\Translation::translate('my-plan.columns.more-info.title') }}</th>
                        <th style="width: 5%">{{ \App\Helpers\Translation::translate('my-plan.columns.interest.title') }}</th>
                        <th style="width: 45%">{{ \App\Helpers\Translation::translate('my-plan.columns.measure.title') }}</th>
                        <th style="width: 12%">{{ \App\Helpers\Translation::translate('my-plan.columns.costs.title') }}</th>
                        <th style="width: 12%">{{ \App\Helpers\Translation::translate('my-plan.columns.savings-costs.title') }}</th>
                        <th style="width: 9%">{{ \App\Helpers\Translation::translate('my-plan.columns.advice-year.title') }}</th>
                        <th style="width: 9%">{{ \App\Helpers\Translation::translate('my-plan.columns.planned-year.title') }}</th>
                    </tr>
                    </thead>
                <tbody>

            @foreach($stepAdvices as $stepSlug => $advicesForStep)
                @foreach($advicesForStep as $advice)
	                <?php $step = \App\Models\Step::where('slug', $stepSlug)->first(); ?>
                    <tr>
                        <input type="hidden" name="advice[{{ $advice->id }}][{{$stepSlug}}][measure_type]" value="{{$measureType}}">
                        <input type="hidden" class="measure_short" value="{{$advice->measureApplication->short}}">
                        <td>
                            <a type="#" data-toggle="collapse" data-target="#more-info-{{$advice->id}}"> <i class="glyphicon glyphicon-chevron-down"></i> </a>
                        </td>

                        <td>
                            <input @if(\App\Helpers\HoomdossierSession::isUserObserving()) disabled="disabled" @endif class="interested-checker" name="advice[{{ $advice->id }}][{{$stepSlug}}][interested]" value="1" type="checkbox" id="advice-{{$advice->id}}-planned" @if($advice->planned) checked @endif />
                        </td>
                        <td>
                            {{ $advice->measureApplication->measure_name }} <a href="#warning-modal" role="button" class="measure-warning" data-toggle="modal" style="display:none;"><i class="glyphicon glyphicon-warning-sign" role="button" data-toggle="modal" title="" style="color: #ffc107"></i></a>
                        </td>
                        <td>
                            &euro; {{ \App\Helpers\NumberFormatter::format($advice->costs, 0, true) }}
                        </td>
                        <td>
                            &euro; {{ \App\Helpers\NumberFormatter::format($advice->savings_money, 0, true) }}
                        </td>
                        <td class="advice-year">
                            {{ $advice->year }}
                        </td>
                        <td>
                            <input @if(\App\Helpers\HoomdossierSession::isUserObserving()) disabled="disabled" @endif type="text" maxlength="4" size="4" class="form-control planned-year" name="advice[{{ $advice->id }}][{{ $stepSlug }}][planned_year]" value="{{ $advice->planned_year }}" />
                        </td>
                    </tr>
                    <tr class="collapse" id="more-info-{{$advice->id}}" >
                        <td colspan="2"></td>
                        <td colspan="">
                            <strong>{{ \App\Helpers\Translation::translate('my-plan.columns.savings-gas.title') }}:</strong>
                            <br>
                            <strong>{{ \App\Helpers\Translation::translate('my-plan.columns.savings-electricity.title') }}:</strong>
                        </td>
                        <td>
                            {{ \App\Helpers\NumberFormatter::format($advice->savings_gas, 0, true) }} m<sup>3</sup>
                            <br>
                            {{ \App\Helpers\NumberFormatter::format($advice->savings_electricity, 0, true) }} kWh
                        </td>
                        <td colspan="3">
                        </td>
                    </tr>
                @endforeach
            @endforeach
                    </tbody>
                </table>
                @if(!\App\Helpers\HoomdossierSession::isUserObserving())
                <a href="{{route('cooperation.conversation-requests.index',  ['cooperation' => $cooperation, 'action' => \App\Models\PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION])}}" class="btn btn-primary">@lang('woningdossier.cooperation.tool.my-plan.conversation-requests.request')</a>
                @endif
            </div>

        </div>
    @endforeach
    </form>


    <div class="row">
        <div class="plan-preview col-md-12">
            <h2>{{ \App\Helpers\Translation::translate('my-plan.maintenance-plan.title') }}</h2>
            <ul id="years">

            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <?php
                $myActionPlanComment = $actionPlanComments->where('input_source_id', \App\Helpers\HoomdossierSession::getInputSource())->first();
            ?>
            @if(!\App\Helpers\HoomdossierSession::isUserObserving())
            <form action="{{route('cooperation.tool.my-plan.store-comment')}}" method="post">
                {{csrf_field()}}
            @endif
                <div class="form-group">
                    <label for="" class=" control-label">
                        <i data-target="#my-plan-own-comment-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                        {{\App\Helpers\Translation::translate('general.specific-situation.title')}} ({{\App\Models\InputSource::find(\App\Helpers\HoomdossierSession::getInputSource())->name}})
                    </label>

                    <textarea name="comment" class="form-control">{{old('comment', $myActionPlanComment instanceof \App\Models\UserActionPlanAdviceComments ? $myActionPlanComment->comment : '')}}</textarea>

                    @component('cooperation.tool.components.help-modal', ['id' => 'my-plan-own-comment-info'])
                        {{\App\Helpers\Translation::translate('general.specific-situation.title')}}
                    @endcomponent
                </div>
                @if(!\App\Helpers\HoomdossierSession::isUserObserving())
                    <button type="submit" class="btn btn-primary">@lang('woningdossier.cooperation.tool.my-plan.add-comment')</button>
            </form>
                @endif
        </div>
    </div>


    <br>
    @if($file instanceof \App\Models\FileStorage)
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">@lang('default.buttons.download')</div>
                <div class="panel-body">
                    <ol>
                        <li>
                            <a @if(!$fileType->isBeingProcessed() )
                               href="{{route('cooperation.file-storage.download', [
                                    'fileType' => $fileType->short,
                                    'fileStorageFilename' => $file->filename
                               ])}}" @endif>{{$fileType->name}} ({{$file->created_at->format('Y-m-d H:i')}})</a>
                        </li>
                    </ol>
                </div>
            </div>
            <hr>
        </div>
    </div>
    @endif

    @if($buildingHasCompletedGeneralData)
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <form action="{{route('cooperation.file-storage.store', ['fileType' => $fileType->short])}}" method="post">
                    {{csrf_field()}}
                    <button style="margin-top: -35px"
                            @if($fileType->isBeingProcessed()) disabled="disabled" type="button" data-toggle="tooltip"
                            title="{{\App\Helpers\Translation::translate('woningdossier.cooperation.admin.cooperation.reports.index.table.report-in-queue')}}"
                            @else
                            type="submit"
                            @endif
                            class="btn btn-{{$fileType->isBeingProcessed()  ? 'warning' : 'primary'}}"
                    >
                        {{ \App\Helpers\Translation::translate('my-plan.download.title') }}
                        @if($fileType->isBeingProcessed() )
                            <span class="glyphicon glyphicon-repeat fast-right-spinner"></span>
                        @endif
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif


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

    $(document).ready(function() {
        const ROOF_INSULATION_FLAT_REPLACE_CURRENT = "roof-insulation-flat-replace-current";
        const REPLACE_ROOF_INSULATION = "replace-roof-insulation";

        const ROOF_INSULATION_PITCHED_REPLACE_TILES = "roof-insulation-pitched-replace-tiles";
        const REPLACE_TILES = "replace-tiles";

        $(window).keydown(function (event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });

        $("select, input[type=radio], input[type=text], input[type=checkbox]").change(function(){

            // var data = $(this).parent().parent().find('input').serialize();

            var data = $(this).closest("form").serialize();
            $.ajax({
                type: "POST",
                url: '{{ route('cooperation.tool.my-plan.store', [ 'cooperation' => $cooperation ]) }}',
                data: data,
                success: function(data){

                    $("ul#years").html("");
                    $.each(data, function(year, steps){
                        var slugYear = year;
                        var header = "<h1>" + year + "</h1>";

                        var table = "<table class=\"table table-responsive\"><thead><tr><th style=\"width: 8%\">{{ \App\Helpers\Translation::translate('my-plan.columns.more-info.title') }}</th><th style=\"width: 62%\">{{ \App\Helpers\Translation::translate('my-plan.columns.measure.title') }}</th><th style=\"width: 15%\">{{ \App\Helpers\Translation::translate('my-plan.columns.costs.title') }}</th><th style=\"width: 15%\">{{ \App\Helpers\Translation::translate('my-plan.columns.savings-costs.title') }}</th><th>{{ \App\Helpers\Translation::translate('my-plan.columns.take-action.title') }}</th></tr></thead><tbody>";
                        var totalCosts = 0;
                        var totalSavingsGas = 0;
                        var totalSavingsElectricity = 0;
                        var totalSavingsMoney = 0;

                        $.each(steps, function (stepName, stepMeasures) {

                            $.each(stepMeasures, function (i, stepData) {

                            if (stepData.interested) {
                                $("#advice-" + stepData.advice_id + "-planned").attr('checked', true)
                            }

                            totalCosts += parseFloat(stepData.costs);
                            totalSavingsGas += parseFloat(stepData.savings_gas);
                            totalSavingsElectricity += parseFloat(stepData.savings_electricity);
                            totalSavingsMoney += parseFloat(stepData.savings_money);

                            var slug = stepName.replace(/\s+/g, '');

                                table += "<tr><td><a type=\"#\" class='turn-on-click' data-toggle=\"collapse\" data-target=\"#more-personal-plan-info-" + slug + "-" + i + "-" + slugYear + "\"><i class=\"glyphicon glyphicon-chevron-down\"></i></a></td><td>" + stepData.measure + "</td><td>&euro; " + Math.round(stepData.costs).toLocaleString('{{ app()->getLocale() }}') + "</td><td>&euro; " + Math.round(stepData.savings_money).toLocaleString('{{ app()->getLocale() }}') + "</td><td><div class='input-group'><div class='input-group-btn'><button class='take-action btn btn-default' type='button'>{{ \App\Helpers\Translation::translate('my-plan.columns.take-action.title') }}</button><button data-toggle='dropdown' class='btn btn-default dropdown-toggle' type='button'><span class='caret'></span> </button> <ul class='dropdown-menu'><li><a href='{{ url('request/coach-conversation') }}'><span>@lang('woningdossier.cooperation.tool.my-plan.options.'.\App\Models\PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION)</span></a></li><li><a href='{{ url('request/more-information') }}/"+stepData.measure_short+"'><span>@lang('woningdossier.cooperation.tool.my-plan.options.'.\App\Models\PrivateMessage::REQUEST_TYPE_MORE_INFORMATION)</span></a></li><li><a href='{{ url('request/other') }}/"+stepData.measure_short+"'><span>@lang('woningdossier.cooperation.tool.my-plan.options.'.\App\Models\PrivateMessage::REQUEST_TYPE_OTHER)</span></a></li></ul></div></div></td></tr>";
                                table += " <tr class='collapse' id='more-personal-plan-info-" + slug + "-" + i + "-" + slugYear + "' > <td colspan='1'></td><td colspan=''> <strong>{{ \App\Helpers\Translation::translate('my-plan.columns.savings-gas.title') }}:</strong> <br><strong>{{ \App\Helpers\Translation::translate('my-plan.columns.savings-electricity.title') }}:</strong> </td><td>"+ Math.round(stepData.savings_gas).toLocaleString('{{ app()->getLocale() }}') +" m<sup>3</sup> <br>"+Math.round(stepData.savings_electricity).toLocaleString('{{ app()->getLocale() }}')+" kWh </td><td colspan='1'></td></tr>";
                        });

                    });

                    // total calculation
                    table += "<tr><td><a type='#' class='turn-on-click' data-toggle='collapse' data-target='#total-costs-" + slugYear + "-total'> <i class=\"glyphicon glyphicon-chevron-down\"></i> </a> </td><td><strong>Totaal</strong></td><td><strong>&euro; " + Math.round(totalCosts).toLocaleString('{{ app()->getLocale() }}') + "</strong></td><td><strong>&euro; " + Math.round(totalSavingsMoney).toLocaleString('{{ app()->getLocale() }}') + "</strong></td><td colspan='1'></td></tr>";
                    table += "<tr class='collapse' id='total-costs-" + slugYear + "-total' > <td colspan='1'></td><td colspan=''> <strong>{{ \App\Helpers\Translation::translate('my-plan.columns.savings-gas.title') }}:</strong> <br><strong>{{ \App\Helpers\Translation::translate('my-plan.columns.savings-electricity.title') }}:</strong> </td><td>" + Math.round(totalSavingsGas).toLocaleString('{{ app()->getLocale() }}') + " m<sup>3</sup> <br>" + Math.round(totalSavingsElectricity).toLocaleString('{{ app()->getLocale() }}') + " kWh </td><td colspan='1'> </td></tr>";

                    table += "</tbody></table>";

                    $("ul#years").append("<li>" + header + table + "</li>");
                    @if(\App\Helpers\HoomdossierSession::isUserObserving())
                        // so if the user is observeringthere is no point in using this button, we disable it and remove the dropdown.
                        $('.take-action').addClass('disabled').attr('disabled', 'disabled').next().remove();
                    @endif
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

                    $('.take-action').click(function () {
                        window.location.href = '{{route('cooperation.conversation-requests.index', ['cooperation' => $cooperation])}}'
                    });

                checkCoupledMeasuresAndMaintenance();

                }
            });

        });

        // Trigger the change event so it will load the data
        $('form').find('*').filter(':input:visible:not(button):first').trigger('change');

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
                        setWarning(ROOF_INSULATION_FLAT_REPLACE_CURRENT, '{{ \App\Helpers\Translation::translate('my-plan.warnings.roof-insulation.check-order.title') }}');
                        setWarning(REPLACE_ROOF_INSULATION, '{{ \App\Helpers\Translation::translate('my-plan.warnings.roof-insulation.check-order.title') }}');
                    }
                    else {
                        // both were planned
                        if (getPlannedYear(ROOF_INSULATION_FLAT_REPLACE_CURRENT) !== getPlannedYear(REPLACE_ROOF_INSULATION)) {
                            // set warning
                            setWarning(ROOF_INSULATION_FLAT_REPLACE_CURRENT, '{{ \App\Helpers\Translation::translate('my-plan.warnings.roof-insulation.planned-year.title') }}');
                            setWarning(REPLACE_ROOF_INSULATION, '{{ \App\Helpers\Translation::translate('my-plan.warnings.roof-insulation.planned-year.title') }}');
                        }
                    }
                }

                // pitched roof
                if (getPlanned(ROOF_INSULATION_PITCHED_REPLACE_TILES)) {
                    if (!getPlanned(REPLACE_TILES)) {
                        // set warning
                        setWarning(ROOF_INSULATION_PITCHED_REPLACE_TILES, '{{ \App\Helpers\Translation::translate('my-plan.warnings.roof-insulation.check-order.title') }}');
                        setWarning(REPLACE_TILES, '{{ \App\Helpers\Translation::translate('my-plan.warnings.roof-insulation.check-order.title') }}');
                    }
                    else {
                        // both were planned
                        if (getPlannedYear(ROOF_INSULATION_PITCHED_REPLACE_TILES) !== getPlannedYear(REPLACE_TILES)) {
                            // set warning
                            setWarning(ROOF_INSULATION_PITCHED_REPLACE_TILES, '{{ \App\Helpers\Translation::translate('my-plan.warnings.roof-insulation.planned-year.title') }}');
                            setWarning(REPLACE_TILES, '{{ \App\Helpers\Translation::translate('my-plan.warnings.roof-insulation.planned-year.title') }}');
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

