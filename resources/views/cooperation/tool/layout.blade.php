@extends('cooperation.layouts.app')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center">
                    @include('cooperation.tool.includes.top-alerts')
                    @include('cooperation.tool.parts.progress')
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                {{--
                   We check if the current step is the building detailI
                --}}
                @if($currentSubStep instanceof \App\Models\Step)
                    <h2>{{$currentStep->name}}</h2>
                @endif
                <ul class="nav nav-tabs mt-20">

                    @if(isset($currentStep))
                        <?php $subStepsForStep = $cooperation->getSubStepsForStep($currentStep); ?>
                        @if($subStepsForStep->isEmpty())
                            <li class="active @if($building->hasCompleted($currentStep)) completed @endif">
                                <a href="{{route("cooperation.tool.{$currentStep->short}.index")}}">{{$currentStep->name}}</a>
                            </li>
                        @endif
                        @foreach($subStepsForStep as $subStep)
                            <li class="@if($subStep->short == $currentSubStep->short) active @endif @if($building->hasCompleted($subStep)) completed @endif">
                                <a href="{{route("cooperation.tool.{$currentStep->short}.{$subStep->short}.index")}}">{{$subStep->name}}</a>
                            </li>
                        @endforeach
                    @endif

                    @if(isset($currentStep) && $currentStep->hasQuestionnaires())
                        @foreach($currentStep->questionnaires as $questionnaire)

                            @if($questionnaire->isActive())
                                <li class="@if($buildingOwner->hasCompletedQuestionnaire($questionnaire)) completed @endif">
                                    <a href="#questionnaire-{{$questionnaire->id}}"
                                       data-toggle="tab">{{$questionnaire->name}}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                </ul>

                <div class="tab-content">
                    @include('cooperation.layouts.custom-questionnaire')

                    <div class="panel tab-pane active tab-pane panel-default" id="main-tab">
                        <div class="panel-heading">
                            <h3>
                                @yield('step_title', $currentSubStep->name ?? $currentStep->name ?? '')
                            </h3>

                            @if(!in_array(Route::currentRouteName(), ['cooperation.tool.index', 'cooperation.tool.my-plan.index']) && !\App\helpers\HoomdossierSession::isUserObserving())
                                <button class="pull-right btn btn-primary submit-main-form">
                                    @if(in_array(Route::currentRouteName(), ['cooperation.tool.ventilation-information.index', 'cooperation.tool.heat-pump.index']))
                                        @lang('default.buttons.next-page')
                                    @else
                                        @lang('default.buttons.next')
                                    @endif
                                </button>
                            @elseif(in_array(Route::currentRouteName(), ['cooperation.tool.my-plan.index']) && $buildingHasCompletedGeneralData && \App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole(['coach', 'resident', 'coordinator', 'cooperation-admin']))
                                <form action="{{route('cooperation.file-storage.store', ['fileType' => $pdfReportFileType->short])}}"
                                      method="post">
                                    {{csrf_field()}}
                                    <button style="margin-top: -35px" type="submit"
                                            class="pull-right btn btn-primary pdf-report">
                                        {{ \App\Helpers\Translation::translate('my-plan.download.title') }}
                                    </button>
                                </form>
                            @endif
                            <div class="clearfix"></div>
                        </div>

                        <div class="panel-body">
                            @yield('step_content', '')
                        </div>

                        <div class="panel-footer bg-white">
                            @if(!\App\helpers\HoomdossierSession::isUserObserving() && !Request::routeIs('cooperation.tool.my-plan.index'))
                                <div class="row">
                                    <div class="col-sm-6">
                                        <?php
                                        // some way of determining the previous step

                                        if ($currentSubStep instanceof \App\Models\Step) {
                                            $subStepsForCurrentStep = $currentStep->subSteps;
                                            $previousStep = $subStepsForCurrentStep->where('order', '<', $currentSubStep->order)->last();
                                        } else {
                                            $previousStep = $steps->where('order', '<', $currentSubStep->order ?? $currentStep->order)->last();
                                        }

                                        if ($currentSubStep instanceof \App\Models\Step && $previousStep instanceof \App\Models\Step) {
                                            $previousUrl = route("cooperation.tool.{$currentStep->short}.{$previousStep->short}.index");
                                        } elseif ($previousStep instanceof \App\Models\Step) {
                                            $previousUrl = route("cooperation.tool.{$previousStep->short}.index");
                                        }
                                        ?>
                                        @if($previousStep instanceof \App\Models\Step)
                                            <a class="btn btn-success pull-left"
                                               href="{{$previousUrl}}">@lang('default.buttons.prev')</a>
                                        @endif
                                    </div>
{{--                                    @if(Route::currentRouteName() === 'cooperation.tool.heat-pump.index')--}}
{{--                                        @lang('default.buttons.next-page')--}}
{{--                                        <div class="col-sm-6">--}}
{{--                                            <a href="" class="pull-right btn btn-primary submit-main-form">--}}
{{--                                                @lang('default.buttons.next-page')--}}
{{--                                            </a>--}}
{{--                                        </div>--}}
{{--                                    @else--}}
                                    <div class="col-sm-6">
                                        <button class="pull-right btn btn-primary submit-main-form">
                                            @lang('default.buttons.next')
                                        </button>
                                    </div>
{{--                                    @endif--}}
                                </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script>

        function removeErrors()
        {
            $('.has-error').removeClass('has-error')
            $('.help-block').remove()
        }
        function addError(input, message) {
            var helpBlock = '<span class="help-block"></span>';
            input.parents('.form-group').addClass('has-error');
            input.parents('.form-group').append($(helpBlock).append('<strong>' + message + '</strong>'));
        }

        function removeError(input) {
            input.parents('.has-error').removeClass('has-error');
            input.parents('.form-group').next('.help-block').remove()
        }

        $('input').keypress(function (event) {
            // get the current keycode
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if (keycode === 13) {
                event.preventDefault();
                return false;
            }
        });

        $(document).ready(function () {
            // get the current url
            var url = document.location.href;

            // scroll to top off page for less retarded behaviour
            window.scrollTo(0, 0);

            // check if the current url matches a hashtag
            if (url.match('#')) {
                // see if there is a tab and show it.
                $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
            }

            // set the hash in url
            $('.nav-tabs a').on('shown.bs.tab', function (e) {
                window.location.hash = e.target.hash;
            });

            compareInputSourceValues();
            whenObservingDisableInputs();
        });

        function whenObservingDisableInputs()
        {
            var isUserObservingTool = '{{\App\Helpers\HoomdossierSession::getIsObserving()}}';

            if (isUserObservingTool) {
                var tabContent = $('.tab-content');

                tabContent.find('.form-control').addClass('disabled').prop('disabled', true);
                tabContent.find('input[type=radio]').addClass('disabled').prop('disabled', true);
                tabContent.find('input[type=checkbox]').addClass('disabled').prop('disabled', true);
            }
        }

        $('.submit-main-form').click(function () {
            // submit the main form / tool tab
            $('.panel#main-tab form').submit();
        });

        $('#copy-coach-input').on('submit', function (event) {
            if (confirm('@lang('woningdossier.cooperation.tool.general-data.coach-input.copy.help')')) {

            } else {
                event.preventDefault();
                return false;
            }
        });
        $('#copy-example-building-input').on('submit', function (event) {
            if (confirm('Weet u zeker dat u alle waardes van de voorbeeldwoning wilt overnemen ? Al uw huidige antwoorden zullen worden overschreven door die van de voorbeeldwoning.')) {

            } else {
                event.preventDefault();
                return false;
            }
        });


        function isUserComparingInputSources() {
            var isUserComparingInputSources = '{{\App\Helpers\HoomdossierSession::isUserComparingInputSources()}}';
            if (isUserComparingInputSources) {
                return true;
            }
            return false;
        }

        function inputType(input) {
            return input.prop('type');
        }

        function compareInputSourceValues() {
            if (isUserComparingInputSources()) {
                var formGroups = $('.input-source-group');

                $(formGroups).each(function () {
                    var formGroup = $(this);
                    var ul = formGroup.find('ul');
                    // get the value from the current user
                    var userInputValues = [];
                    var input = formGroup.find('input');

                    switch (inputType(input)) {
                        case 'radio':
                            userInputValues.push(formGroup.find('input[type=radio]:checked').val());
                            break;
                        case 'checkbox':
                            formGroup.find('input[type=checkbox]:checked').each(function () {
                                userInputValues.push($(this).val());
                            });
                            break;
                        default:
                            userInputValues.push(formGroup.find('.form-control').val());
                            break;
                    }

                    var bestCssUGGSDesignStyle = {'background-color': 'red', 'color': 'white'};
                    // get the value from the compare input source
                    var compareInputSourceValue = ul.find('li[data-input-source-short="{{\App\Helpers\HoomdossierSession::getCompareInputSourceShort()}}"]').attr('data-input-value');

                    if (typeof compareInputSourceValue !== "undefined") {

                        if (!userInputValues.includes(compareInputSourceValue)) {

                            switch (inputType(input)) {
                                case 'radio':
                                    input.parent().css(bestCssUGGSDesignStyle);
                                    break;
                                case 'checkbox':
                                    input.parent().css(bestCssUGGSDesignStyle);
                                    break;
                                default:
                                    formGroup.find('.form-control').css(bestCssUGGSDesignStyle);
                                    break;
                            }
                        }
                    }

                })
            }
        }

    </script>
    <script src="{{ asset('js/are-you-sure.js') }}"></script>

    @if(!in_array(Route::currentRouteName(), ['cooperation.tool.my-plan.index']))
        <script>
            $("form.form-horizontal").areYouSure();
        </script>
    @endif

@endpush