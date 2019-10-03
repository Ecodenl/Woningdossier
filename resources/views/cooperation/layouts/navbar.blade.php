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
                @if (\App\Helpers\Hoomdossier::user()->isFillingToolForOtherBuilding())
                    <a href="{{route('cooperation.admin.stop-session')}}" class="btn btn-warning navbar-btn">Stop sessie</a>
                @endif
                @if(\App\Helpers\Hoomdossier::user()->can('access-admin'))
                    <a href="{{ route('cooperation.admin.index') }}" class="btn btn-success navbar-btn">Naar coöperatie omgeving</a>
                @endif
            </ul>
        @endauth

        @if(App::environment() == 'local') {{-- currently only for local development --}}
            <!-- Left Side Of Navbar -->
            <ul class="nav navbar-nav">
                @if(count(config('hoomdossier.supported_locales')) > 1)
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
                @endif
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
                    @if (!\App\Helpers\Hoomdossier::user()->isFillingToolForOtherBuilding())
                    <li>
                        <a href="{{url('/home')}}">@lang('woningdossier.cooperation.navbar.start')</a>
                    </li>
                    @endif
                    <li>
                        <a href="{{
                                    \App\Models\Building::find(\App\Helpers\HoomdossierSession::getBuilding())->hasCompleted(\App\Models\Step::where('slug', 'building-detail')->first()) ? route('cooperation.tool.general-data.index', ['cooperation' => $cooperation]) : route('cooperation.tool.building-detail.index', ['cooperation' => $cooperation])
                                 }}">
                            @lang('woningdossier.cooperation.tool.title')
                        </a>
                    </li>

                    @if (!\App\Helpers\Hoomdossier::user()->isFillingToolForOtherBuilding())

                        <?php
                            $messageUrl = route('cooperation.my-account.messages.index');

                            if(\App\Helpers\Hoomdossier::user()->can('access-admin') && \App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole(['coordinator', 'coach', 'cooperation-admin'])) {
                                $messageUrl = route('cooperation.admin.messages.index');
                            }
                        ?>
                        <li>
                            @include('cooperation.layouts.message-badge', compact('messageUrl'))
                        </li>

                        @include('cooperation.admin.layouts.navbar.role-switcher')

                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                                    {{ \App\Helpers\Hoomdossier::user()->first_name }} {{ \App\Helpers\Hoomdossier::user()->last_name }}<span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu">
                                    <li><a href="{{ route('cooperation.my-account.index', ['cooperation' => $cooperation]) }}">@lang('woningdossier.cooperation.navbar.my-account')</a></li>
                                    <li><a href="{{ route('cooperation.privacy.index', ['cooperation' => $cooperation]) }}">@lang('woningdossier.cooperation.navbar.privacy')</a></li>
                                    <li><a href="{{ route('cooperation.disclaimer.index', ['cooperation' => $cooperation]) }}">@lang('woningdossier.cooperation.navbar.disclaimer')</a></li>
                                    {{--<li><a href="{{ route('cooperation.my-account.cooperations.index', ['cooperation' => $cooperation->slug]) }}">@lang('my-account.cooperations.form.header')</a></li>--}}
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