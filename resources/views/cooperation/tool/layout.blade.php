@extends('cooperation.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                @include('cooperation.tool.includes.top-alerts')
                @include('cooperation.tool.progress')
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                {{--
                   We check if the current step is the building detail
                --}}
                @if($currentStep instanceof \App\Models\Step && in_array($currentStep->slug, ['building-detail',  'general-data']))
                    <ul class="nav nav-tabs">
                        <li @if($currentStep->slug == "building-detail")class="active"@endif>
                            <a href="{{route('cooperation.tool.building-detail.index')}}" >@lang('woningdossier.cooperation.step.building-detail')</a>
                        </li>

                        <li @if($currentStep->slug == "general-data")class="active @endif">
                            <a href="{{route('cooperation.tool.general-data.index')}}">@lang('woningdossier.cooperation.step.general-data')</a>
                        </li>

                        @if(isset($currentStep) && $currentStep->hasQuestionnaires())
                            @foreach($currentStep->questionnaires as $questionnaire)
                                @if($questionnaire->isActive())
                                    <li><a href="#questionnaire-{{$questionnaire->id}}" data-toggle="tab">{{$questionnaire->name}}</a></li>
                                @endif
                            @endforeach
                        @endif
                    </ul>
                @elseif(isset($currentStep) && $currentStep->hasQuestionnaires())
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#main-tab" data-toggle="tab">{{$currentStep->name}}</a></li>
                        @foreach($currentStep->questionnaires as $questionnaire)
                            @if($questionnaire->isActive())
                                <li><a href="#questionnaire-{{$questionnaire->id}}" data-toggle="tab">{{$questionnaire->name}}</a></li>
                            @endif
                        @endforeach
                    </ul>
                @endif

                <div class="tab-content">
                    @include('cooperation.layouts.custom-questionnaire')

                    <div class="panel tab-pane active tab-pane panel-default" id="main-tab">
                        <div class="panel-heading">
                            <h3>
                                @yield('step_title', '')
                            </h3>

                            @if(!in_array(Route::currentRouteName(), ['cooperation.tool.index', 'cooperation.tool.my-plan.index']) && !\App\helpers\HoomdossierSession::isUserObserving())
                                <button id="submit-main-form" class="pull-right btn btn-primary">
                                    @if(in_array(Route::currentRouteName(), ['cooperation.tool.ventilation-information.index', 'cooperation.tool.heat-pump.index']))
                                        @lang('default.buttons.next-page')
                                    @else
                                        @lang('default.buttons.next')
                                    @endif
                                </button>
                            @elseif(in_array(Route::currentRouteName(), ['cooperation.tool.my-plan.index']) && $buildingHasCompletedGeneralData && \App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole(['coach', 'resident', 'coordinator', 'cooperation-admin']))
                                <form action="{{route('cooperation.file-storage.store', ['fileType' => $pdfReportFileType->short])}}" method="post">
                                    {{csrf_field()}}
                                    <button style="margin-top: -35px"
                                            type="submit"
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
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $('input').keypress(function(event) {
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
            })
        });

        $('#submit-main-form').click(function () {
            // submit the main form / tool tab
            $('.panel#main-tab form button[type=submit]').click();
        });

        $('#copy-coach-input').on('submit', function (event) {
            if(confirm('@lang('woningdossier.cooperation.tool.general-data.coach-input.copy.help')')) {

            } else {
                event.preventDefault();
                return false;
            }
        });
        $('#copy-example-building-input').on('submit', function (event) {
            if(confirm('Weet u zeker dat u alle waardes van de voorbeeldwoning wilt overnemen ? Al uw huidige antwoorden zullen worden overschreven door die van de voorbeeldwoning.')) {

            } else {
                event.preventDefault();
                return false;
            }
        });

        $(document).ready(compareInputSourceValues());

        function isUserComparingInputSources()
        {
            var isUserComparingInputSources = '{{\App\Helpers\HoomdossierSession::isUserComparingInputSources()}}';
            if (isUserComparingInputSources) {
                return true;
            }
            return false;
        }
        function inputType(input)
        {
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
                            formGroup.find('input[type=checkbox]:checked').each(function() {
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