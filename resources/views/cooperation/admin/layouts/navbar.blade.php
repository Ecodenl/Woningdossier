<nav class="navbar navbar-default navbar-static-top">
    <div class="container-fluid">
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
                        @foreach(config('hoomdossier.supported_locales') as $locale)
                            @if(app()->getLocale() != $locale)
                                <li>
                                    <a href="{{ route('cooperation.switch-language', ['cooperation' => $cooperation, 'locale' => $locale]) }}">@lang('woningdossier.navbar.languages.'. $locale)</a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </li>
            </ul>

            @if(\App\Helpers\HoomdossierSession::getRole())
                @if(\App\Helpers\Hoomdossier::user()->hasRole('coach|coordinator|cooperation-admin|super-admin|superuser'))
                    <a href="{{ route('cooperation.frontend.tool.quick-scan.my-plan.index') }}" class="btn btn-warning navbar-btn">
                        Naar tool
                    </a>
                @endif
            @endif
        @endif

        <!-- Right Side Of Navbar -->
            <ul class="nav navbar-nav navbar-right">
                <!-- Authentication Links -->
                @guest
                @else
                    @if(\App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole(['coordinator', 'coach', 'cooperation-admin']))
                        <li>
                            @include('cooperation.layouts.message-badge', ['messageUrl' => route('cooperation.admin.messages.index')])
                        </li>

                    @endif

                    @if(\App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole(['super-admin','superuser']))
                        <li><a href="{{ route('cooperation.admin.example-buildings.index') }}">@lang('woningdossier.cooperation.admin.navbar.example-buildings')</a></li>
                    @endif
                    @if(\App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole(['cooperation-admin']))
                        <li><a href="{{ route('cooperation.admin.example-buildings.index') }}">@lang('woningdossier.cooperation.admin.navbar.example-buildings')</a></li>
                    @endif

                    @include('cooperation.admin.layouts.navbar.role-switcher')

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                            {{ \App\Helpers\Hoomdossier::user()->first_name }} {{ \App\Helpers\Hoomdossier::user()->last_name }}<span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu">
                            <li><a href="{{ route('cooperation.my-account.index', ['cooperation' => $cooperation]) }}">@lang('woningdossier.cooperation.my-account.settings.form.index.header')</a></li>
                            {{--<li><a href="{{ route('cooperation.my-account.cooperations.index', ['cooperation' => $cooperation->slug]) }}">@lang('my-account.cooperations.form.header')</a></li>--}}
                            <li>
                                <a href="{{ route('cooperation.auth.logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    Logout
                                </a>

                                <form id="logout-form" action="{{ route('cooperation.auth.logout') }}" method="POST" style="display: none;">
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