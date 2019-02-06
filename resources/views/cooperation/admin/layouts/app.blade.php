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
    <link rel="stylesheet" href="{{ asset('css/datepicker/datetimepicker.min.css') }}">
    @if(isset($cooperationStyle->css_url))
        <link href="{{ asset($cooperationStyle->css_url) }}" rel="stylesheet">
    @endif
    <style>
        .add-space {
            margin: 10px;
            padding: 0 10px 0 10px;
        }
    </style>
    @stack('css')

    <link rel="stylesheet" href="{{asset('css/select2/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/datatables/responsive.dataTables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/datatables/dataTables.bootstrap.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/datatables/responsive.bootstrap.min.css')}}">

</head>
<body class="@yield('page_class')">
<div id="app">

    @include('cooperation.admin.layouts.navbar')
    @include('cooperation.layouts.messages')

    <div class="container">
        <div class="row">
            @include('cooperation.admin.layouts.sidebar-menu')
            <div class="col-md-9">
                @yield('content')
            </div>
        </div>
    </div>


</div>

<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/moment/moment.js') }}"></script>
<script src="{{ asset('js/datepicker/datetimepicker.js') }}"></script>

<script src="{{ asset('js/datatables.js') }}"></script>
<script src="{{ asset('js/disable-auto-fill.js') }}"></script>
<script src="{{asset('js/select2.js')}}"></script>

<script>
    $(document).ready(function () {

        $('.collapse').on('shown.bs.collapse', function () {
            $(this).parent().find(".glyphicon-chevron-down").removeClass("glyphicon-chevron-down").addClass("glyphicon-chevron-up");
        }).on('hidden.bs.collapse', function () {
            $(this).parent().find(".glyphicon-chevron-up").removeClass("glyphicon-chevron-up").addClass("glyphicon-chevron-down");
        });
    });

    $.extend(true, $.fn.dataTable.defaults, {
        language: {
            url: "{{asset('js/datatables-dutch.json')}}"
        }
    });
</script>
@stack('js')
{{--additional js code here--}}
</body>
</html>



