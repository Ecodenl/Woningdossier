@extends('cooperation.frontend.layouts.app')

@section('header')
    <div class="w-full">
        @include('cooperation.frontend.layouts.parts.navbar')
        @if(RouteLogic::inSimpleScan(Route::currentRouteName()))
            {{-- Step progress --}}
            @include('cooperation.frontend.layouts.parts.sub-nav')

            @if(! RouteLogic::inMyPlan(Route::currentRouteName()))
                {{-- Progress bar --}}
                <div class="w-full bg-gray h-2 relative z-40 -mt-1">
                    @php
                        // $total and $current get injected via the SimpleScanComposer in the ViewServiceProvider
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
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-20 flex flex-wrap space-y-20"
         @if(request()->input('iframe', false)) style="padding-top: 0;" @endif>
        @if(RouteLogic::inExpertTool(Route::currentRouteName()))
            @php
                $masterInputSource = \App\Models\InputSource::findByShort(\App\Models\InputSource::MASTER_SHORT);
                //$hasQuestionnaires = $currentStep->questionnaires->count() > 0;
            @endphp

            <div class="flex flex-row flex-wrap w-full items-center justify-between relative z-30">
                <div class="flex flex-row flex-wrap w-full" x-data="tabs()">
{{--                    <h2 class="heading-2">--}}
{{--                        {{$currentStep->name}}--}}
{{--                    </h2>--}}

                        {{--TODO: Disabled tabs for now, load via separate page to decrease page load (maybe ready for deprecation anyway)--}}
{{--                    <ul class="nav-tabs mt-5 hidden" x-ref="nav-tabs">--}}
{{--                        @if($hasQuestionnaires)--}}
{{--                            @foreach($currentStep->questionnaires as $questionnaire)--}}
{{--                                <li class="@if($buildingOwner->hasCompletedQuestionnaire($questionnaire, $masterInputSource)) completed @endif">--}}
{{--                                    <a href="#questionnaire-{{$questionnaire->id}}" x-bind="tab">--}}
{{--                                        {{$questionnaire->name}}--}}
{{--                                    </a>--}}
{{--                                </li>--}}
{{--                            @endforeach--}}
{{--                        @endif--}}
{{--                    </ul>--}}

                    <div class="w-full border border-solid border-blue-500 border-opacity-50 rounded-b-lg rounded-t-lg tab-content"
                         x-ref="tab-content">
{{--                        @if($hasQuestionnaires)--}}
{{--                            @foreach($currentStep->questionnaires as $questionnaire)--}}
{{--                                @include('cooperation.frontend.layouts.parts.custom-questionnaire', [--}}
{{--                                    'questionnaire' => $questionnaire, 'isTab' => true,--}}
{{--                                    'step' => $currentStep, 'showSave' => true,--}}
{{--                                ])--}}
{{--                            @endforeach--}}
{{--                        @endif--}}

                        <div class="w-full divide-y divide-blue-500 divide-opacity-50" id="main-tab" x-ref="main-tab"
                             x-show="currentTab === $el">
                            <div class="px-4 py-8 flex justify-between">
                                <h3 class="heading-3 inline-block">
                                    @yield('step_title', $currentSubStep->name ?? $currentStep->name ?? '')
                                </h3>
                                @if($currentStep->isDynamic() || RouteLogic::inQuestionnaire(Route::currentRouteName()))
                                    <livewire:cooperation.frontend.tool.expert-scan.buttons :scan="$scan ?? $currentStep->scan"
                                                                                            :step="$currentStep"
                                                                                            :questionnaire="$questionnaire ?? null"/>
                                @else
                                    @if(! \App\helpers\HoomdossierSession::isUserObserving())
                                        <button class="float-right btn btn-purple submit-main-form">
                                            @lang('default.buttons.save')
                                        </button>
                                    @endif
                                @endif
                            </div>

                            <div class="px-4 py-8" id="main-content">
                                @yield('content')
                            </div>

                            <div class="px-4 py-8">
                                @if(! \App\helpers\HoomdossierSession::isUserObserving())
                                    @php
                                        // This only shows in expert, and since lite can't go to expert, we just
                                        // fetch the quick scan.
                                        $quickScan = \App\Models\Scan::findByShort(\App\Models\Scan::QUICK);
                                    @endphp
                                    <div class="flex flex-row flex-wrap w-full">
                                        <div class="w-full sm:w-1/2">
                                            <a class="btn btn-green float-left"
                                               href="{{ route('cooperation.frontend.tool.simple-scan.my-plan.index', ['scan' => $quickScan]) }}">
                                                @lang('default.buttons.cancel')
                                            </a>
                                        </div>
                                        <div class="w-full sm:w-1/2">
                                            @if($currentStep->isDynamic() || RouteLogic::inQuestionnaire(Route::currentRouteName()))
                                                <livewire:cooperation.frontend.tool.expert-scan.buttons :scan="$scan ?? $currentStep->scan"
                                                                                                        :step="$currentStep"
                                                                                                        :questionnaire="$questionnaire ?? null"/>
                                            @else
                                                <button class="float-right btn btn-purple submit-main-form">
                                                    @lang('default.buttons.save')
                                                </button>
                                            @endif
                                        </div>
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

        <script>
            $("form.form-horizontal").areYouSure();
        </script>
    @endpush
@endif