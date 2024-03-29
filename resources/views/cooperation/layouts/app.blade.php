<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @stack('meta')

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Styles -->
        <link href="{{ asset('css/appold.css') }}" rel="stylesheet">
        @if(isset($cooperationStyle->css_url))
            <link href="{{ asset($cooperationStyle->css_url) }}" rel="stylesheet">
        @endif
        <style>
            .add-space {
                padding: 0 10px 0 10px;
            }

            [x-cloak] {
                display: none;
            }
        </style>
        @stack('css')
    </head>
    <body class="@yield('page_class')">
        <div id="app">

            @include('cooperation.layouts.navbar')
            @include('cooperation.layouts.messages')
            @yield('content')

        </div>

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}"></script>
        <script src="{{ asset('js/hoomdossier.js') }}"></script>

        <script>
            $(document).ready(function () {
                @if(Auth::check())
                pollForMessageCount();
                @endif
            });
        </script>

        <template id="invalid-feedback-template">
            <span class="help-block">
                <strong></strong>
            </span>
        </template>
        @stack('js')
    </body>
</html>
