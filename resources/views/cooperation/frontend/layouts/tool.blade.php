@extends('cooperation.frontend.layouts.app')

@section('header')
    <div class="w-full">
        @include('cooperation.frontend.layouts.parts.navbar')
        @if(RouteLogic::inQuickScanTool(Route::currentRouteName()))
            {{-- Step progress --}}
            @include('cooperation.frontend.layouts.parts.sub-nav')

            @if(! RouteLogic::inMyPlan(Route::currentRouteName()))
                {{-- Progress bar --}}
                <div class="w-full bg-gray h-2 relative z-40 -mt-1">
                    @php
                        // $total and $current get injected in the QuickScanController via the ViewServiceProvider
                        $total = $total ?? 100;
                        $current = $current ?? 100;
                        $width = 100 / $total * $current;
                    @endphp
                    {{-- Define style-width based on step progress divided by total steps --}}
                    <div class="h-full bg-purple" style="width: {{$width}}%"></div>
                </div>
            @endif
        @endif
    </div>
@endsection

{{-- Remove BG image --}}
@section('main_style', '')

@section('main')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-20 flex flex-wrap space-y-20">
        @if(RouteLogic::inExpertTool(Route::currentRouteName()))
            @php
                $masterInputSource = \App\Models\InputSource::findByShort(\App\Models\InputSource::MASTER_SHORT);
            @endphp
            {{-- Expert tool has a card-wrapper around the content --}}
{{--            @if(Auth::check() && ! Hoomdossier::user()->hasRoleAndIsCurrentRole(RoleHelper::ROLE_RESIDENT))--}}
{{--                <div class="flex flex-row flex-wrap w-full items-center justify-between relative z-30">--}}
{{--                    @include('cooperation.tool.includes.top-alerts')--}}
{{--                    @include('cooperation.tool.parts.progress')--}}
{{--                </div>--}}
{{--            @endif--}}

            <div class="flex flex-row flex-wrap w-full items-center justify-between relative z-30">
                <div class="flex flex-row flex-wrap w-full" x-data="tabs()">
{{--                    @if($currentSubStep instanceof \App\Models\Step)--}}
{{--                        <h2 class="heading-2">--}}
{{--                            {{$currentStep->name}}--}}
{{--                        </h2>--}}
{{--                    @endif--}}
                    <ul class="nav-tabs mt-5 hidden" x-ref="nav-tabs">
                        @if(isset($currentStep))
                            @php
                                $subStepsForStep = $currentStep->children;
                            @endphp
                            @if($subStepsForStep->isEmpty())
                                <li class="active @if($building->hasCompleted($currentStep, $masterInputSource)) completed @endif">
                                    <a href="{{route("cooperation.tool.{$currentStep->short}.index")}}">
                                        {{$currentStep->name}}
                                    </a>
                                </li>
                            @endif
                            @foreach($subStepsForStep as $subStep)
                                <li class="@if($subStep->short == $currentSubStep->short) active @endif @if($building->hasCompleted($subStep, $masterInputSource)) completed @endif">
                                    <a href="{{route("cooperation.tool.{$currentStep->short}.{$subStep->short}.index")}}">
                                        {{$subStep->name}}
                                    </a>
                                </li>
                            @endforeach
                        @endif

                        @if(isset($currentStep) && $currentStep->hasQuestionnaires())
                            @foreach($currentStep->questionnaires as $questionnaire)

                                @if($questionnaire->isActive())
                                    <li class="@if($buildingOwner->hasCompletedQuestionnaire($questionnaire, $masterInputSource)) completed @endif">
                                        <a href="#questionnaire-{{$questionnaire->id}}" x-bind="tab">
                                            {{$questionnaire->name}}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    </ul>

                    <div class="w-full border border-solid border-blue-500 border-opacity-50 rounded-b-lg rounded-t-lg tab-content"
                        x-ref="tab-content">
                        @if(isset($currentStep) && $currentStep->hasQuestionnaires())
                            @foreach($currentStep->questionnaires as $questionnaire)
                                @if($questionnaire->isActive())
                                    @include('cooperation.frontend.layouts.parts.custom-questionnaire', [
                                        'questionnaire' => $questionnaire, 'isTab' => true
                                    ])
                                @endif
                            @endforeach
                        @endif


                        <div class="w-full divide-y divide-blue-500 divide-opacity-50" id="main-tab" x-ref="main-tab"
                             x-show="currentTab === $el">
                            <div class="px-4 py-8">
                                <h3 class="heading-3 inline-block">
                                    @yield('step_title', $currentSubStep->name ?? $currentStep->name ?? '')
                                </h3>

                                @if(!in_array(Route::currentRouteName(), ['cooperation.tool.index', 'cooperation.tool.my-plan.index']) && !\App\helpers\HoomdossierSession::isUserObserving())
                                    <button class="float-right btn btn-purple submit-main-form">
                                        @if(in_array(Route::currentRouteName(), ['cooperation.tool.ventilation-information.index', 'cooperation.tool.heat-pump.index']))
                                            @lang('default.buttons.next-page')
                                        @else
                                            @lang('default.buttons.save')
                                        @endif
                                    </button>
                                @elseif(in_array(Route::currentRouteName(), ['cooperation.tool.my-plan.index']) && $buildingHasCompletedGeneralData && Auth::check() && Hoomdossier::user()->hasRoleAndIsCurrentRole(['coach', 'resident', 'coordinator', 'cooperation-admin']))
                                    <form action="{{route('cooperation.file-storage.store', ['fileType' => $pdfReportFileType->short])}}"
                                          method="post">
                                        @csrf
                                        <button style="margin-top: -35px" type="submit"
                                                class="float-right btn btn-purple pdf-report">
                                            {{ \App\Helpers\Translation::translate('my-plan.download.title') }}
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <div class="px-4 py-8" id="main-content">
                                @yield('content')
                            </div>

                            <div class="px-4 py-8">
                                @if(!\App\helpers\HoomdossierSession::isUserObserving() && !Request::routeIs('cooperation.tool.my-plan.index'))
                                    <div class="flex flex-row flex-wrap w-full">
                                        <div class="w-full sm:w-1/2">
                                            <a class="btn btn-green float-left"
                                               href="{{ route('cooperation.frontend.tool.quick-scan.my-plan.index') }}">
                                                @lang('default.buttons.cancel')
                                            </a>
                                        </div>
                                        @if(Route::currentRouteName() === 'cooperation.tool.heat-pump.index')
                                            <div class="w-full sm:w-1/2">
                                                <a href="" class="float-right btn btn-purple submit-main-form">
                                                    @lang('default.buttons.next-page')
                                                </a>
                                            </div>
                                        @else
                                            <div class="w-full sm:w-1/2">
                                                <button class="float-right btn btn-purple submit-main-form">
                                                    @lang('default.buttons.save')
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            @yield('content')
        @endif
    </div>
@endsection

@if(RouteLogic::inExpertTool(Route::currentRouteName()))
    @push('js')
        <script>
            $('input').keypress(function (event) {
                // get the current keycode
                var keycode = (event.keyCode ? event.keyCode : event.which);
                // Enter
                if (keycode === 13) {
                    event.preventDefault();
                    return false;
                }
            });

            $(document).ready(function () {
                // scroll to top off page for less clunky behaviour
                window.scrollTo(0, 0);

                compareInputSourceValues();
                whenObservingDisableInputs();
            });

            function whenObservingDisableInputs()
            {
                var isUserObservingTool = '{{\App\Helpers\HoomdossierSession::getIsObserving()}}';

                if (isUserObservingTool) {
                    var tabContent = $('.tab-content');

                    tabContent.find('.form-input').addClass('disabled').prop('disabled', true);
                    tabContent.find('input[type=radio]').addClass('disabled').prop('disabled', true);
                    tabContent.find('input[type=checkbox]').addClass('disabled').prop('disabled', true);
                }
            }

            $('.submit-main-form').click(function () {
                // submit the main form / tool tab
                $('#main-tab #main-content form').submit();
                $('.submit-main-form').prop('disabled', 'disabled').addClass('disabled');
            });

            $('#copy-coach-input').on('submit', function (event) {
                if (confirm('@lang('woningdossier.cooperation.tool.general-data.coach-input.copy.help')')) {

                } else {
                    event.preventDefault();
                    return false;
                }
            });
            $('#copy-example-building-input').on('submit', function (event) {
                if (confirm('@lang('woningdossier.cooperation.tool.general-data.example-building-input.copy.help')')) {

                } else {
                    event.preventDefault();
                    return false;
                }
            });


            function isUserComparingInputSources() {
                let isUserComparingInputSources = '{{\App\Helpers\HoomdossierSession::isUserComparingInputSources()}}';
                return !!isUserComparingInputSources;
            }

            function inputType(input) {
                return input.prop('type');
            }

            function compareInputSourceValues() {
                if (isUserComparingInputSources()) {
                    {{-- TODO: Check these classes --}}
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
@endif