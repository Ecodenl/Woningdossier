@extends('cooperation.layouts.app')

@section('header')
    <div class="w-full">
        @include('cooperation.frontend.layouts.parts.navbar')
        @if(RouteLogic::inSimpleScan(Route::currentRouteName()))
            {{-- Step progress --}}
            @include('cooperation.frontend.layouts.parts.sub-nav')

            @if(! RouteLogic::inMyPlan(Route::currentRouteName()) && ! RouteLogic::inMyRegulations(Route::currentRouteName()))
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
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-20 flex flex-wrap space-y-20 tool-layout"
         @if(request()->input('iframe', false)) style="padding-top: 0;" @endif>
        @if(RouteLogic::inExpertTool(Route::currentRouteName()))
            @php
                $masterInputSource = \App\Models\InputSource::findByShort(\App\Models\InputSource::MASTER_SHORT);
            @endphp

            <div class="flex flex-row flex-wrap w-full items-center justify-between relative z-30">
                <div class="flex flex-row flex-wrap w-full" x-data>
{{--                    <h2 class="heading-2">--}}
{{--                        {{$currentStep->name}}--}}
{{--                    </h2>--}}

                    <div class="w-full border border-solid border-blue-500 border-opacity-50 rounded-b-lg rounded-t-lg tab-content">
                        <div class="w-full divide-y divide-blue-500 divide-opacity-50" id="main-tab">
                            <div class="px-4 py-8 flex justify-between">
                                <h3 class="heading-3 inline-block">
                                    @yield('step_title', $currentSubStep->name ?? $currentStep->name ?? '')
                                </h3>
                                @if($currentStep->isDynamic() || RouteLogic::inQuestionnaire(Route::currentRouteName()))
                                    <livewire:cooperation.frontend.tool.expert-scan.buttons :scan="$scan"
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
        <script type="module">
            $('input').keypress(function (event) {
                // get the current keycode
                var keycode = (event.keyCode ? event.keyCode : event.which);
                // Enter
                if (keycode === 13) {
                    event.preventDefault();
                    return false;
                }
            });

            document.addEventListener('DOMContentLoaded', function () {
                // scroll to top off page for less clunky behaviour
                window.scrollTo(0, 0);

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

            function inputType(input) {
                return input.prop('type');
            }
        </script>
    @endpush
@endif