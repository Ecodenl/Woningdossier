@extends('cooperation.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <?php
                    if(!isset($building)) {
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

                @include('cooperation.tool.progress')
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                @if(in_array(Route::currentRouteName(), ['cooperation.tool.general-data.index']) && Auth::user()->hasRole('resident') || app()->environment() == "local")
                    <div class="row">
                        <div class="col-sm-12">
                            <form id="copy-coach-input" action="{{route('cooperation.import.copy')}}" method="post" class="pull-left">
                                <input type="hidden" name="input_source" value="coach">
                                {{csrf_field()}}
                                <button class="btn btn-primary">
                                    @lang('woningdossier.cooperation.tool.general-data.coach-input.copy.title')
                                </button>
                            </form>

                            <form id="copy-example-building-input" action="{{route('cooperation.import.copy')}}" method="post" class="pull-right">
                                <input type="hidden" name="input_source" value="example-building">
                                {{csrf_field()}}
                                <button class="btn btn-primary">
                                    Neem voorbeeldwoning antwoorden over
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

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
    </script>
    <script src="{{ asset('js/are-you-sure.js') }}"></script>

    @if(!in_array(Route::currentRouteName(), ['cooperation.tool.my-plan.index']))
    <script>
        $("form.form-horizontal").areYouSure();
    </script>
    @endif

@endpush