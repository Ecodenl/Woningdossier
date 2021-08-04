@extends('cooperation.frontend.layouts.app')

@section('header')
    <div class="w-full">
        @include('cooperation.frontend.layouts.parts.navbar')
        @if(\App\Helpers\Blade\RouteLogic::inQuickScanTool(\Illuminate\Support\Facades\Route::currentRouteName()))
            {{-- Step progress --}}
            <div class="flex items-center justify-between w-full bg-blue-100 border-b-1 h-16 px-5 xl:px-20 relative z-30">
                <div class=" flex items-center h-full">
                    <i class="icon-sm icon-check-circle-dark mr-1"></i>
                    <span class="text-blue">Woninggegevens</span>
                </div>
                <div class="step-divider-line"></div>
                <div class="flex items-center h-full">
                    <i class="icon-sm bg-purple bg-opacity-25 rounded-full border border-solid border-purple mr-1"></i>
                    <span class="text-purple">Gebruik</span>
                </div>
                <div class="step-divider-line"></div>
                <div class="flex items-center h-full">
                    <i class="icon-sm bg-transparent rounded-full border border-solid border-blue mr-1"></i>
                    <span class="text-blue">Woonwensen</span>
                </div>
                <div class="step-divider-line"></div>
                <div class="flex items-center h-full">
                    <i class="icon-sm bg-transparent rounded-full border border-solid border-blue mr-1"></i>
                    <span class="text-blue">Woonstatus</span>
                </div>
                <div class="step-divider-line"></div>
                <div class="flex items-center h-full">
                    <i class="icon-sm bg-transparent rounded-full border border-solid border-blue mr-1"></i>
                    <span class="text-blue">Overige</span>
                </div>
                <div class="border border-blue-500 border-opacity-50 h-1/2"></div>
                <div class="flex items-center justify-start h-full">
                    <i class="icon-sm icon-house-dark mr-1"></i>
                    <span class="text-blue">Woonplan</span>
                </div>
            </div>
            {{-- Progress bar --}}
            <div class="w-full bg-gray h-2">
                {{-- Define style-width based on step progress divided by total steps --}}
                <div class="h-full bg-purple" style="width: 30%"></div>
            </div>
        @endif
    </div>
@endsection

{{-- Remove BG image --}}
@section('main_style', '')

@section('main')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-20 flex flex-wrap space-y-20">
        @if(\App\Helpers\Blade\RouteLogic::inExpertTool(\Illuminate\Support\Facades\Route::currentRouteName()))
            <div class="flex flex-row flex-wrap w-full items-center justify-between relative z-30">
                @include('cooperation.tool.includes.top-alerts')
            </div>
        @endif

        @yield('content')
    </div>
@endsection

@if(\App\Helpers\Blade\RouteLogic::inExpertTool(\Illuminate\Support\Facades\Route::currentRouteName()))
    @push('js')
        <script>
{{-- TODO: Check the usages of these scripts --}}

            function removeErrors()
            {
                $('.has-error').removeClass('has-error');
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
@endif