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
    <link rel="stylesheet" href="{{asset('css/frontend/app.css')}}">

    @livewireStyles
    @stack('css')
</head>
<body>

@yield('header')
<?php
    $background = optional($cooperation->firstMedia(MediaHelper::BACKGROUND))->getUrl();
    $background = empty($background) ? asset('images/background.jpg') : $background;
?>
<main class="bg-cover bg-center bg-no-repeat bg-white"
      style="@yield('main_style', 'background-image: url(\''. $background .'\');')">
{{--    @include('cooperation.frontend.layouts.parts.messages')--}}

    @yield('main')
</main>

@livewireScripts
<script src="https://cdn.jsdelivr.net/gh/livewire/turbolinks@v0.1.x/dist/livewire-turbolinks.js" data-turbolinks-eval="false"></script>
{{-- Ensure Livewire is above app.js -> Alpine is loaded in app.js and must be loaded after Livewire --}}
<script src="{{ mix('js/app.js') }}"></script>

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
    })
</script>
@stack('js')
</body>
</html>