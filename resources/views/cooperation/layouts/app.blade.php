<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @if(isset($cooperationStyle->css_url))
        <link href="{{ asset($cooperationStyle->css_url) }}" rel="stylesheet">
    @endif
    <style>
        .add-space {
            padding: 0 10px 0 10px;
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

    function updateTotalUnreadMessages () {

        $.ajax({
            url: window.location.origin + '/messages/count',
            method: 'GET',
            success: function (response) {
                $('#total-unread-message-count').html(response.count);
            }
        });
    };

    // first the timeout is not set, so we set it to 0
    // after that we timeout to 3 seconds.
    var beenPolled = false;

    function pollForMessageCount () {

        var timeout = 0;

        setTimeout(function () {
            if (beenPolled) {
                timeout = 10000
            }
            beenPolled = true;
            updateTotalUnreadMessages();
            pollForMessageCount();
        }, timeout);
    };


    $(document).ready(function () {
        pollForMessageCount();
    });

    function hoomdossierRound(valueToRound, bucket)
    {
        if (typeof bucket === "undefined") {
            bucket = 5;
        }

        return Math.round(valueToRound / bucket) * bucket;


    }
</script>
@stack('js')
</body>
</html>
