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
    <link rel="stylesheet" href="{{asset('css/datepicker/datetimepicker.min.css')}}">
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
</head>
<body class="@yield('page_class')">
@if(App::environment() == 'local')
    <?php
        session('role_id') == "" ? print_r("No role has been set yet.") : print_r("role id: ".session('role_id')) ;
    ?>
@endif
<div id="app">

    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse" aria-expanded="false">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ route('cooperation.admin.index') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
            @if(App::environment() == 'local') {{-- currently only for local --}}
            <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                            @lang('woningdossier.navbar.language')<span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            @foreach(config('woningdossier.supported_locales') as $locale)
                                @if(app()->getLocale() != $locale)
                                    <li>
                                        <a href="{{ route('cooperation.switch-language', ['cooperation' => $cooperation, 'locale' => $locale]) }}">@lang('woningdossier.navbar.languages.'. $locale)</a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </li>
                </ul>
            @endif

            <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @guest

                    @else
                        @hasrole('cooperation-admin|super-admin|superuser')
                            <li><a href="{{ route('cooperation.admin.cooperation.cooperation-admin.example-buildings.index') }}">@lang('woningdossier.cooperation.admin.navbar.example-buildings')</a></li>
                            <li><a href="{{ route('cooperation.admin.cooperation.cooperation-admin.reports.index') }}">@lang('woningdossier.cooperation.admin.navbar.reports')</a></li>
                        @endhasrole
                        @if(Auth::user()->getRoleNames()->count() == 1)
                            <li>
                                <a>
                                    @lang('woningdossier.cooperation.admin.navbar.current-role') {{ Auth::user()->getHumanReadableRoleName(Auth::user()->getRoleNames()->first()) }}
                                </a>
                            </li>

                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                                    @if(session()->exists('role_id'))@lang('woningdossier.cooperation.admin.navbar.current-role') {{\Spatie\Permission\Models\Role::find(session('role_id'))->human_readable_name}}<span class="caret"></span>@endif
                                </a>

                                <ul class="dropdown-menu">
                                    @foreach(Auth::user()->roles as $role)
                                        <li>
                                            <a href="{{\App\Helpers\RoleHelper::getUrlByRoleName($role->name)}}">
                                                {{$role->human_readable_name}}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endif
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                                {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}<span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu">
                                <li><a href="{{ route('cooperation.my-account.settings.index', ['cooperation' => $cooperation]) }}">@lang('woningdossier.cooperation.my-account.settings.form.index.header')</a></li>
                                {{--<li><a href="{{ route('cooperation.my-account.cooperations.index', ['cooperation' => $cooperation->slug]) }}">@lang('woningdossier.cooperation.my-account.cooperations.form.header')</a></li>--}}
                                <li>
                                    <a href="{{ route('cooperation.admin.logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>

                                    <form id="logout-form" action="{{ route('cooperation.admin.logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    @include('cooperation.layouts.messages')
    @yield('content')

</div>

<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/moment/moment.js') }}"></script>
<script src="{{ asset('js/datepicker/datetimepicker.js') }}"></script>

@stack('js')
</body>
</html>
