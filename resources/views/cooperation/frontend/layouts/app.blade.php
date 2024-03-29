<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">

        @stack('meta')

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('page_title', config('app.name', 'Laravel'))</title>

        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/frontend/app.css') }}">

        @livewireStyles
        <style>
            [x-cloak] {
                display: none;
            }
        </style>
        @stack('css')

        <script>
            function agentHas(keyword) {
                return navigator.userAgent.toLowerCase().search(keyword.toLowerCase()) > -1;
            }

            // Safari glitches out when scrolling in the modal. This fixes it using hardware acceleration.
            // It also ruins z-index. So we make it relative PURELY for safari...
            if ((!! window.ApplePaySetupFeature || !! window.safari) && agentHas("Safari") && ! agentHas("Chrome") && ! agentHas("CriOS")) {
                let style = document.createElement('style');
                document.head.appendChild(style);
                style.appendChild(
                    document.createTextNode(
                        `.modal-container * { -webkit-transform: translate3d(0,0,0); } .modal .select-dropdown { position: relative; }`
                    )
                );
            }
        </script>
    </head>
    <body id="app-body" style="overflow: auto;">
        @if(! request()->input('iframe', false))
            @yield('header')
        @endif
        @php
            $background = optional(optional($cooperation)->firstMedia(MediaHelper::BACKGROUND))->getUrl() ?: asset('images/background.jpg');
        @endphp
        <main class="bg-cover bg-center bg-no-repeat bg-white"
              style="@yield('main_style', 'background-image: url(\''. $background .'\');')">
            {{--    @include('cooperation.frontend.layouts.parts.messages')--}}

            @yield('main')
        </main>

        @livewireScripts
        {{--<script src="https://cdn.jsdelivr.net/gh/livewire/turbolinks@v0.1.x/dist/livewire-turbolinks.js" data-turbolinks-eval="true"></script>--}}
        {{-- Ensure Livewire is above app.js -> Alpine is loaded in app.js and must be loaded after Livewire --}}
        <script src="{{ mix('js/app.js') }}"></script>
        <script src="{{asset('js/tinymce/tinymce.min.js')}}"></script>
        <script src="{{ mix('js/hoomdossier.js') }}"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Bind simple function to remove errors when clicked
                let formErrors = document.getElementsByClassName('form-error');

                for (let i = 0; i < formErrors.length; i++) {
                    formErrors[i].addEventListener('click', function () {
                        this.classList.remove('form-error');
                        this.querySelector('.form-error-label').remove();
                    }, {once: true});
                }
            });

            // Handle Polyfill for IOS 10
            window.addEventListener('touchmove', function () {
            });

            window.addEventListener('modal-toggled', function (event) {
                let scrollable = document.getElementById('app-body');
                let modalOpened = event.detail.modalOpened;
                // Handle scrollbar of the scrollable content when modal is opened
                if (modalOpened) {
                    // We can't use Tailwind classes since the specificity on them triggers only INSIDE the app-body
                    scrollable.style.overflow = 'hidden';
                } else {
                    scrollable.style.overflow = 'auto';
                }
            });
        </script>

        <template id="invalid-feedback-template">
            <p class="form-error-label">
            </p>
        </template>
        @stack('js')
    </body>
</html>