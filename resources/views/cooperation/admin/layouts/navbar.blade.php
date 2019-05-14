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

            @if(\App\Helpers\HoomdossierSession::getRole())
                @hasrole('coach|coordinator|cooperation-admin|super-admin|superuser')
                <a href="{{ route('cooperation.tool.index') }}" class="btn btn-warning navbar-btn">Naar tool</a>
                @endhasrole
        @endif
        @endif

        <!-- Right Side Of Navbar -->
            <ul class="nav navbar-nav navbar-right">
                <!-- Authentication Links -->
                @guest
                @else
                    @if(Auth::user()->hasRoleAndIsCurrentRole(['coordinator', 'coach', 'cooperation-admin']))
                        <li>
                            @switch($roleShort = \App\Models\Role::find(\App\Helpers\HoomdossierSession::getRole())->name)
                                @case('coach')
                                    <?php $messageUrl = route('cooperation.admin.messages.index'); ?>
                                    @break
                                @case('coordinator')
                                    <?php $messageUrl = route('cooperation.admin.messages.index'); ?>
                                    @break
                                @case('cooperation-admin')
                                    <?php $messageUrl = route('cooperation.admin.messages.index'); ?>
                                    @break
                                @default
                                <?php $messageUrl = route('cooperation.admin.index'); ?>
                            @endswitch
                            <a href="{{$messageUrl}}">
                                <span class="glyphicon glyphicon-envelope"></span>
                                <span class="badge">{{$myUnreadMessagesCount}}</span>
                            </a>
                        </li>
                    @endif

                    @if(Auth::user()->hasRoleAndIsCurrentRole(['super-admin','superuser']))
                        <li><a href="{{ route('cooperation.admin.example-buildings.index') }}">@lang('woningdossier.cooperation.admin.navbar.example-buildings')</a></li>
                    @endif
                    @if(Auth::user()->hasRoleAndIsCurrentRole(['cooperation-admin']))
                        <li><a href="{{ route('cooperation.admin.example-buildings.index') }}">@lang('woningdossier.cooperation.admin.navbar.example-buildings')</a></li>
                    @endif

                    @include('cooperation.admin.layouts.navbar.role-switcher')

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