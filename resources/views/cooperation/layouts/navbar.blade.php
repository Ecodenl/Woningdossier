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
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'Hoomdossier') }}
            </a>
        </div>

        <div class="collapse navbar-collapse" id="app-navbar-collapse">
        @auth
            <ul class="nav navbar-nav">
                @if (Auth::user()->isFillingToolForOtherBuilding())
                    <a href="{{route('cooperation.admin.index')}}" class="btn btn-warning navbar-btn">Stop sessie</a>
                @endif
            </ul>
        @endauth

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
                @auth
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                            @lang('woningdossier.navbar.input_source')<span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            @foreach($inputSources as $inputSource)
                                @if(\App\Models\BuildingFeature::withoutGlobalScope(\App\Scopes\GetValueScope::class)->where('input_source_id', $inputSource->id)->first() instanceof \App\Models\BuildingFeature)
                                    <li>
                                        <a href="{{ route('cooperation.input-source.change-input-source-value', ['cooperation' => $cooperation, 'input_source_value_id' => $inputSource->id]) }}">{{$inputSource->name}}</a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </li>
                @endauth
            </ul>
        @endif
        <!-- Right Side Of Navbar -->
            <ul class="nav navbar-nav navbar-right">
                <!-- Authentication Links -->
                @guest
                    <li><a href="{{ route('cooperation.login', ['cooperation' => $cooperation]) }}">@lang('auth.login.form.header')</a></li>
                    <li><a href="{{ route('cooperation.register', ['cooperation' => $cooperation]) }}">@lang('auth.register.form.header')</a></li>
                @else
                    <li><a href="{{ route('cooperation.tool.index', ['cooperation' => $cooperation]) }}">@lang('woningdossier.cooperation.tool.title')</a></li>
                    @if (!Auth::user()->isFillingToolForOtherBuilding())
                    <li><a href="{{route('cooperation.my-account.messages.index', ['cooperation' => $cooperation])}}"><span class="glyphicon glyphicon-envelope"></span> <span class="badge">{{$myUnreadMessages->count()}}</span></a></li>
    {{--                    <li><a href="{{ route('cooperation.help.index', ['cooperation' => $cooperation]) }}">@lang('woningdossier.cooperation.help.title')</a></li>--}}
                        <li><a href="{{ route('cooperation.measures.index', ['cooperation' => $cooperation]) }}">@lang('woningdossier.cooperation.measure.title')</a></li>
                        <li><a href="{{ url('/home') }}">@lang('woningdossier.cooperation.disclaimer.title')</a></li>

                    @if(\App\Helpers\HoomdossierSession::getRole() && Auth::user()->getRoleNames()->count() > 1)
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                                @lang('woningdossier.cooperation.admin.navbar.current-role') {{ \Spatie\Permission\Models\Role::find(\App\Helpers\HoomdossierSession::getRole())->human_readable_name }}<span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu">
                                @foreach(Auth::user()->roles()->orderBy('level', 'DESC')->get() as $role)
                                    <li>
                                        <a href="{{ route('cooperation.admin.switch-role', ['role' => $role->name, 'return']) }}">
                                            {{ $role->human_readable_name }}
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
                                    <a href="{{ route('cooperation.logout', ['cooperation' => $cooperation]) }}"
                                       onclick="event.preventDefault();
                                                         document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>

                                <form id="logout-form" action="{{ route('cooperation.logout', ['cooperation' => $cooperation]) }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                            <li>
                                <span class="pull-right" style="padding-right:.5em;line-height:100%;"><small>v{{ config('app.version') }}@if(App::environment() != 'production') - {{ App::environment() }}@endif</small></span>
                            </li>
                        </ul>
                    </li>
                    @endif
                @endguest
            </ul>
        </div>
    </div>
</nav>