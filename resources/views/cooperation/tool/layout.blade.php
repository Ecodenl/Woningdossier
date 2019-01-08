@extends('cooperation.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <?php
                    if (! isset($building)) {
                        $building = \App\Models\Building::find(\App\Helpers\HoomdossierSession::getBuilding());
                    }
                ?>
                @if (Auth::user()->buildings->first()->id != \App\Helpers\HoomdossierSession::getBuilding())
                    <div class="col-sm-6">
                        @component('cooperation.tool.components.alert', ['alertType' => 'info', 'dismissible' => false])
                            @lang('woningdossier.cooperation.tool.filling-for', [
                                'first_name' => \App\Models\User::find(\App\Models\Building::find(\App\Helpers\HoomdossierSession::getBuilding())->user_id)->first_name,
                                'last_name' => \App\Models\User::find(\App\Models\Building::find(\App\Helpers\HoomdossierSession::getBuilding())->user_id)->last_name,
                                'input_source_name' => \App\Models\InputSource::find(\App\Helpers\HoomdossierSession::getInputSourceValue())->name
                            ])
                        @endcomponent
                    </div>
                    <div class="col-sm-6">
                        @component('cooperation.tool.components.alert', ['alertType' => 'info', 'dismissible' => false])
                            @lang('woningdossier.cooperation.tool.current-building-address', [
                                'street' => $building->street,
                                'number' => $building->number,
                                'extension' => $building->extension,
                                'zip_code' => $building->postal_code,
                                'city' => $building->city
                            ])
                        @endcomponent
                    </div>
                @else
                    @if(\App\Helpers\HoomdossierSession::isUserComparingInputSources())
                        <form id="copy-input-{{\App\Helpers\HoomdossierSession::getCompareInputSourceShort()}}" action="{{route('cooperation.import.copy')}}" method="post">
                            <input type="hidden" name="input_source" value="{{\App\Helpers\HoomdossierSession::getCompareInputSourceShort()}}">
                            {{csrf_field()}}
                        </form>
                        <div class="row">
                            <div class="col-sm-6">
                                @component('cooperation.tool.components.alert', ['alertType' => 'info', 'dismissible' => false])
                                    <div class="row">
                                        <div class="col-sm-6">
                                            @lang('woningdossier.cooperation.tool.is-user-comparing-input-sources', ['input_source_name' => \App\Models\InputSource::findByShort(\App\Helpers\HoomdossierSession::getCompareInputSourceShort())->name])
                                        </div>
                                        <div class="col-sm-6">
                                            <a onclick="$('#copy-input-{{\App\Helpers\HoomdossierSession::getCompareInputSourceShort()}}').submit()" class="btn btn-block btn-sm btn-primary pull-right">
                                                @lang('woningdossier.cooperation.tool.general-data.coach-input.copy.title')
                                            </a>
                                            <a href="{{route('cooperation.my-account.import-center.set-compare-session', ['inputSourceShort' => \App\Models\InputSource::find(\App\Helpers\HoomdossierSession::getInputSource())->short])}}" class="btn btn-block btn-sm btn-primary pull-right">
                                                Stop vergelijking
                                            </a>
                                        </div>
                                    </div>
                                @endcomponent
                            </div>
                            <div class="col-sm-6">
                                @component('cooperation.tool.components.alert', ['alertType' => 'info', 'dismissible' => false])
                                    @lang('woningdossier.cooperation.tool.current-building-address', [
                                        'street' => $building->street,
                                        'number' => $building->number,
                                        'extension' => $building->extension,
                                        'zip_code' => $building->postal_code,
                                        'city' => $building->city
                                    ])
                                @endcomponent
                            </div>
                        </div>
                    @else
                        @component('cooperation.tool.components.alert', ['alertType' => 'info', 'dismissible' => false])
                            @lang('woningdossier.cooperation.tool.current-building-address', [
                                'street' => $building->street,
                                'number' => $building->number,
                                'extension' => $building->extension,
                                'zip_code' => $building->postal_code,
                                'city' => $building->city
                            ])
                        @endcomponent
                    @endif
                @endif

                @include('cooperation.tool.progress')
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                @if(isset($currentStep) && $currentStep->hasQuestionnaires())
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

                            @if(!in_array(Route::currentRouteName(), ['cooperation.tool.index', 'cooperation.tool.my-plan.index']))
                                <button id="submit-main-form" class="pull-right btn btn-primary">
                                    @if(in_array(Route::currentRouteName(), ['cooperation.tool.ventilation-information.index', 'cooperation.tool.heat-pump.index']))
                                        @lang('default.buttons.next-page')
                                    @else
                                        @lang('default.buttons.next')
                                    @endif
                                </button>
                            @else
                                @if(in_array(Route::currentRouteName(), ['cooperation.tool.my-plan.index']))
                                    <a href="{{ route('cooperation.tool.my-plan.export', ['cooperation' => $cooperation]) }}" class="pull-right btn btn-primary">
                                        @lang('woningdossier.cooperation.tool.my-plan.download')
                                    </a>
                                @endif
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
            var isUserComparingInputSources = '{{\App\Helpers\HoomdossierSession::isUserComparingInputSources()}}'
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
                    var userInputValue = formGroup.find('.form-control').val();
                    var bestCssUGGSDesignStyle = {'background-color': 'red', 'color': 'white'};
                    // get the value from the compare input source
                    var compareInputSourceValue = ul.find('li[data-input-source-short="{{\App\Helpers\HoomdossierSession::getCompareInputSourceShort()}}"]').attr('data-input-value');

                    if (typeof compareInputSourceValue !== "undefined") {

                        if (userInputValue !== compareInputSourceValue) {
                            var input = formGroup.find('input');


                            switch (inputType(input)) {
                                case 'radio':
                                    input.parent().css(bestCssUGGSDesignStyle);
                                    break;
                                case 'checkbox':
                                    console.log('hoi');
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