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
                    <li><a href="{{ route('cooperation.login', ['cooperation' => $cooperation]) }}">@lang('auth.login.form.header')</a></li>
                    <li><a href="{{ route('cooperation.register', ['cooperation' => $cooperation]) }}">@lang('auth.register.form.header')</a></li>
                @else
                    <li><a href="{{ route('cooperation.tool.index', ['cooperation' => $cooperation]) }}">@lang('woningdossier.cooperation.tool.title')</a></li>
                    <li><a href="{{ route('cooperation.help.index', ['cooperation' => $cooperation]) }}">@lang('woningdossier.cooperation.help.title')</a></li>
                    <li><a href="{{ route('cooperation.measures.index', ['cooperation' => $cooperation]) }}">@lang('woningdossier.cooperation.measure.title')</a></li>
                    <li><a href="{{ url('/home') }}">@lang('woningdossier.cooperation.disclaimer.title')</a></li>

                    @if(Auth::user()->getRoleNames()->count() == 1 && Auth::user()->getRoleNames()->first() == "resident")
                        <li>
                            <a href="{{route('cooperation.my-account.messages.index', ['cooperation' => $cooperation])}}">
                                <span class="glyphicon glyphicon-envelope"></span>
                                <span class="badge">{{$myUnreadMessages->count()}}</span>
                            </a>
                        </li>
                    @elseif(Auth::user()->getRoleNames()->count() == 1)
                        <li>
                            <a href="{{route('cooperation.admin.index', ['role' => Auth::user()->getRoleNames()->first()])}}">
                                <span class="glyphicon glyphicon-envelope"></span>
                                <span class="badge">{{$myUnreadMessages->count()}}</span>
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{route('cooperation.admin.index')}}">
                                <span class="glyphicon glyphicon-envelope"></span>
                                <span class="badge">{{$myUnreadMessages->count()}}</span>
                            </a>
                        </li>
                    @endif
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                            {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}<span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu">
                            <li><a href="{{ route('cooperation.my-account.index', ['cooperation' => $cooperation]) }}">@lang('woningdossier.cooperation.my-account.settings.form.index.header')</a></li>
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
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>