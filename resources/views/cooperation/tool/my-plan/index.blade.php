@extends('cooperation.tool.layout')

@section('step_title', \App\Helpers\Translation::translate('my-plan.title.title'))

@section('page_class', 'page-my-plan')

@section('step_content')


    <div class="row">
        <div class="col-md-12">
            <p>{!! \App\Helpers\Translation::translate('my-plan.description.title') !!}</p>
            @foreach($inputSourcesForPersonalPlanModal as $inputSource)
                <button type="button" class="btn btn-default" data-toggle="modal"
                        data-target="#{{$inputSource->name}}">{{ \App\Helpers\Translation::translate('my-plan.trigger-modal-for-other-input-source.title', ['input_source_name' => strtolower($inputSource->name)]) }}</button>
            @endforeach
        </div>
    </div>

            {{-- Create the modals with personal plan info for the other input source --}}

    @foreach($personalPlanForVariousInputSources as $inputSourceName => $measuresByYear)
        @include('cooperation.tool.my-plan.parts.modal-for-other-input-source')
    @endforeach

    {{-- Our plan, which the users can edit --}}
    @include('cooperation.tool.my-plan.parts.my-plan-form')

    {{-- The personal plan, will be generated with js --}}
    @include('cooperation.tool.my-plan.parts.personal-plan')

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
                            <i data-target="#my-plan-own-comment-info"
                               class="glyphicon glyphicon-info-sign glyphicon-padding collapsed"
                               aria-expanded="false"></i>
                            @lang('general.specific-situation.title')
                            ({{\App\Models\InputSource::find(\App\Helpers\HoomdossierSession::getInputSource())->name}})
                        </label>

                        <textarea @if(\App\Helpers\HoomdossierSession::isUserObserving()) disabled="disabled" @endif name="comment"
                                  class="form-control">{{old('comment', $myActionPlanComment instanceof \App\Models\UserActionPlanAdviceComments ? $myActionPlanComment->comment : '')}}</textarea>

                        @component('cooperation.tool.components.help-modal', ['id' => 'my-plan-own-comment-info'])
                            {{\App\Helpers\Translation::translate('general.specific-situation.title')}}
                        @endcomponent
                    </div>
                    @if(!\App\Helpers\HoomdossierSession::isUserObserving())
                        <button type="submit"
                                class="btn btn-primary">@lang('woningdossier.cooperation.tool.my-plan.add-comment')</button>
                </form>
            @endif
        </div>
    </div>



    <br>
    {{--    @if($file instanceof \App\Models\FileStorage && \App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole(['coach', 'resident']))--}}
    @if(\App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole(['coach', 'resident', 'coordinator', 'cooperation-admin']))
    <div class="row" id="download-section" style="display: none;">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">@lang('default.buttons.download')</div>
                <div class="panel-body">
                    <ol>
                        <li class="download-link">
                        </li>
                    </ol>
                </div>
            </div>
            <hr>
        </div>
    </div>
    @endif

    <br>
    @if($buildingHasCompletedGeneralData && \App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole(['coach', 'resident', 'coordinator', 'cooperation-admin']))
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <form action="{{route('cooperation.file-storage.store', ['fileType' => $pdfReportFileType])}}"
                          method="post">
                        {{csrf_field()}}
                        <button style="margin-top: -35px" type="submit" class="btn btn-primary pdf-report">
                            {{ \App\Helpers\Translation::translate('my-plan.download.title') }}
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


        var reportInQueueTranslation = '@lang('woningdossier.cooperation.admin.cooperation.reports.index.table.report-in-queue')';
        var pdfReportButton = $('button.pdf-report');
        var checkIfFileIsBeingProcessedRoute = '{{route('cooperation.file-storage.check-if-file-is-being-processed', ['fileType' => $pdfReportFileType])}}';

        function pollForFileProcessing() {
            $.get(checkIfFileIsBeingProcessedRoute, function (response) {

                if (response.is_file_being_processed === true) {
                    $('#download-section').show();
                    if (pdfReportButton.find('span').length === 0) {
                        disableGenerateReportButton();
                    }
                } else {
                    if (response.file_download_link.length > 0) {
                        addReportDownloadLink(response);
                    }
                    $('#download-section').show();
                    enableGenerateReportButton();
                    // hide the first alert, which is the report gets downloaded alert
                    $('.alert').first().alert('close');
                }


                // only poll when the file is being processed.
                if (response.is_file_being_processed) {
                    setTimeout(pollForFileProcessing, 5000);
                }
            });
        }

        // disable the button, add a title, warning color and a spinner.
        function disableGenerateReportButton() {
            pdfReportButton.prop('disabled', true).prop('title', reportInQueueTranslation).prop('type', 'button').addClass('btn-warning').removeClass('btn-primary').append(
                '<span class="glyphicon glyphicon-repeat fast-right-spinner"></span>'
            )
        }

        // enable it, remove the disable srtuff
        function enableGenerateReportButton() {
            pdfReportButton.removeAttr('disabled').removeAttr('title').prop('type', 'submit').addClass('btn-primary').removeClass('btn-warning');
            pdfReportButton.find('span').remove();
        }

        // add the download link, and show the panel.
        function addReportDownloadLink(response) {
            var downloadLink = $('<a>').prop('href', response.file_download_link).append(response.file_type_name + ' (' + response.file_created_at + ')');
            $('li.download-link').append(downloadLink);
        }    $(document).ready(function () {var pageHasAlreadyBeenScrolledToDownloadSection = false;
            const ROOF_INSULATION_FLAT_REPLACE_CURRENT = "roof-insulation-flat-replace-current";
            const REPLACE_ROOF_INSULATION = "replace-roof-insulation";

            const ROOF_INSULATION_PITCHED_REPLACE_TILES = "roof-insulation-pitched-replace-tiles";
            const REPLACE_TILES = "replace-tiles";
            const MEASURE = '{{\App\Models\PrivateMessage::REQUEST_TYPE_MEASURE}}';
            // build the base route, we can replace te params later on.
            var conversationRequestRoute = '{{route('cooperation.conversation-requests.index', ['action' => 'action', 'measureApplicationShort' => 'measure_application_short'])}}';$(window).keydown(function (event) {
                if (event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });

            if (window.location.hash !== "") {
                pollForFileProcessing();
            }

            $("select, input[type=radio], input[type=text], input[type=checkbox]").change(function () {

                // var data = $(this).parent().parent().find('input').serialize();

                var data = $(this).closest("form").serialize();
                $.ajax({
                    type: "POST",
                    url: '{{ route('cooperation.tool.my-plan.store', [ 'cooperation' => $cooperation ]) }}',
                    data: data,
                    success: function (data) {

                        $("ul#years").html("");
                        $.each(data, function (year, steps) {
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

                                    table += "<tr>" +
                                        "<td>" +
                                        "<a type=\"#\" class='turn-on-click' data-toggle=\"collapse\" data-target=\"#more-personal-plan-info-" + slug + "-" + i + "-" + slugYear + "\">" +
                                        "<i class=\"glyphicon glyphicon-chevron-down\"></i>" +
                                        "</a>" +
                                        "</td>" +
                                        "<td>" + stepData.measure + "</td><td>&euro; " + Math.round(stepData.costs).toLocaleString('{{ app()->getLocale() }}') + "</td><td>&euro; " + Math.round(stepData.savings_money).toLocaleString('{{ app()->getLocale() }}') + "</td><td>" +
                                        "<a href='"+conversationRequestRoute.replace('action', MEASURE).replace('measure_application_short', stepData.measure_short)+"' class='take-action btn btn-default' type='button'>@lang('my-plan.columns.take-action.title')</a></td></tr>";
                                    table += " <tr class='collapse' id='more-personal-plan-info-" + slug + "-" + i + "-" + slugYear + "' > <td colspan='1'></td><td colspan=''> <strong>{{ \App\Helpers\Translation::translate('my-plan.columns.savings-gas.title') }}:</strong> <br><strong>{{ \App\Helpers\Translation::translate('my-plan.columns.savings-electricity.title') }}:</strong> </td><td>" + Math.round(stepData.savings_gas).toLocaleString('{{ app()->getLocale() }}') + " m<sup>3</sup> <br>" + Math.round(stepData.savings_electricity).toLocaleString('{{ app()->getLocale() }}') + " kWh </td><td colspan='1'></td></tr>";
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


                // only when its not done yet, otherwise on every change it will scroll to the download section
                        if (!pageHasAlreadyBeenScrolledToDownloadSection && window.location.hash.length > 0) {
                        // we will have to do this after the change, otherwise it will be scrolled to the download section. And then the personal plan appends and poof its gone.
                            $('html, body').animate({
                                scrollTop: $(window.location.hash).offset().top
                            }, 'slow');
                        }

                        pageHasAlreadyBeenScrolledToDownloadSection = true;
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

            $(".interested-checker").click(function () {
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
            function getPlanned(maShort) {
                var row = getMeasureRow(maShort);
                if (row !== null) {
                    return row.find('input.interested-checker').is(':checked');
                }
                return false;
            }

            // Returns the planned year for the measure. Either the user-defined
            // year or advised year, if both are not set we get the current year.
            function getPlannedYear(maShort) {
                var row = getMeasureRow(maShort);
                if (row !== null) {
                    var planned = row.find('input.planned-year').val();
                    if (planned === '') {
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
            function getMeasureRow(maShort) {
                var element = $("input.measure_short[value=" + maShort + "]");
                if (element.length) {
                    return element.parent();
                }
                return null;
            }

            // Set a warning for a measure application
            function setWarning(maShort, warning) {
                var row = getMeasureRow(maShort);
                var link = row.find('a.measure-warning');
                var icon = link.find('i');
                icon.attr('title', warning);
                link.show();
            }

            function removeWarnings() {
                $("a.measure-warning").hide();
            }

        });
    </script>
@endpush

