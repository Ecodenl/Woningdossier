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

</head>
<body id="app-body" style="overflow: auto;">
@if(! request()->input('iframe', false))
    @yield('header')
@endif
@php
    // So the cooperation should always be set but sometimes it is not? We are not sure how so we do some logging
    $background = null;
    if (isset($cooperation) && $cooperation instanceof \App\Models\Cooperation) {
        $background = optional($cooperation->firstMedia(MediaHelper::BACKGROUND))->getUrl();
    } else {
        \App\Services\DiscordNotifier::init()->notify("Cooperation is not set! URL: " . request()->fullUrl() . "; Route: " . optional(request()->route())->getName() . "; Cooperation ID according to session: " . \App\Helpers\HoomdossierSession::getCooperation() . "; Running in console: " . app()->runningInConsole());
    }
    $background = empty($background) ? asset('images/background.jpg') : $background;
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
    window.addEventListener( 'touchmove', function() {});

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
@stack('js')
</body>

</html>