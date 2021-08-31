{{-- Nav bar --}}
<div class="flex flex-wrap flex-row justify-between items-center w-full bg-white h-12 px-5 xl:px-20 relative z-100 shadow-lg">
    <div class="flex flex-row flex-wrap justify-between items-center space-x-4">
        {{-- TODO: Check if this should be interchangable per cooperation --}}
        <a href="{{ route('cooperation.welcome') }}">
            <i class="icon-hoomdossier"></i>
        </a>
        @auth
            @if (\App\Helpers\Hoomdossier::user()->isFillingToolForOtherBuilding())
                <a href="{{route('cooperation.admin.stop-session')}}" class="btn btn-yellow">
                    @lang('cooperation/frontend/layouts.navbar.stop-session')
                </a>
            @endif
            {{-- only show the "back to cooperation button when the user is an admin without resident role AND we're on the settings page AND there's only one --}}
            @if(\App\Helpers\Hoomdossier::user()->can('access-admin') && !\App\Helpers\Hoomdossier::user()->hasRole('resident') && \App\Helpers\Hoomdossier::user()->getRoleNames()->count() <= 1 && !\App\Helpers\Hoomdossier::user()->isFillingToolForOtherBuilding())
                <a href="{{ route('cooperation.admin.index') }}" class="btn btn-green">
                    @lang('cooperation/frontend/layouts.navbar.back-to-cooperation')
                </a>
            @endif
        @endauth
        @if(App::environment() == 'local') {{-- currently only for local development --}}
            @if(count(config('hoomdossier.supported_locales')) > 1)
                @component('cooperation.frontend.layouts.components.dropdown', ['label' => __('default.language')])
                    @foreach(config('hoomdossier.supported_locales') as $locale)
                        @if(app()->getLocale() != $locale)
                            <li>
                                <a href="{{ route('cooperation.switch-language', ['cooperation' => $cooperation, 'locale' => $locale]) }}">
                                    @lang('default.languages.'. $locale)
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endcomponent
            @endif
            @auth
                @component('cooperation.frontend.layouts.components.dropdown', ['label' => __('cooperation/frontend/layouts.navbar.input-source')])
                    @foreach($inputSources as $inputSource)
                        @if(\App\Models\BuildingFeature::withoutGlobalScope(\App\Scopes\GetValueScope::class)->where('input_source_id', $inputSource->id)->first() instanceof \App\Models\BuildingFeature)
                            <li>
                                <a href="{{ route('cooperation.input-source.change-input-source-value', ['cooperation' => $cooperation, 'input_source_value_id' => $inputSource->id]) }}">{{$inputSource->name}}</a>
                            </li>
                        @endif
                    @endforeach
                @endcomponent
            @endauth
        @endif
    </div>
    <div class="flex flex-row justify-end items-center space-x-4">
        @if(\App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole('resident'))
            @if (!\App\Helpers\Hoomdossier::user()->isFillingToolForOtherBuilding())
                <p>
                    <a href="{{ route('cooperation.home') }}">
                        @lang('default.start')
                    </a>
                </p>
            @endif
            <p>
                <a class="text-blue" href="{{ route('cooperation.tool.general-data.index') }}">
                    @lang('cooperation/frontend/layouts.navbar.advise')
                </a>
            </p>
        @endif

        @if (!\App\Helpers\Hoomdossier::user()->isFillingToolForOtherBuilding())
            @if(\App\Helpers\Hoomdossier::user()->getRoleNames()->count() > 1 && \App\Helpers\HoomdossierSession::hasRole())
                @component('cooperation.frontend.layouts.components.dropdown', ['label' => __('cooperation/frontend/layouts.navbar.current-role') . \Spatie\Permission\Models\Role::find(\App\Helpers\HoomdossierSession::getRole())->human_readable_name])
                    @foreach(\App\Helpers\Hoomdossier::user()->roles()->orderBy('level', 'DESC')->get() as $role)
                        <li>
                            <a href="{{ route('cooperation.admin.switch-role', ['role' => $role->name]) }}">
                                {{ $role->human_readable_name }}
                            </a>
                        </li>
                    @endforeach
                @endcomponent
            @endif

            @livewire('cooperation.frontend.layouts.parts.messages')

            @component('cooperation.frontend.layouts.components.dropdown', ['label' => '<i class="icon-md icon-account-circle"></i>'])
                <li>
                    <a href="{{ route('cooperation.my-account.index', ['cooperation' => $cooperation]) }}">
                        @lang('woningdossier.cooperation.navbar.my-account')
                    </a>
                </li>
                <li>
                    <a href="{{ route('cooperation.privacy.index', ['cooperation' => $cooperation]) }}">
                        @lang('woningdossier.cooperation.navbar.privacy')
                    </a>
                </li>
                <li>
                    <a href="{{ route('cooperation.disclaimer.index', ['cooperation' => $cooperation]) }}">
                        @lang('woningdossier.cooperation.navbar.disclaimer')
                    </a>
                </li>
{{--                <li>--}}
{{--                    <a href="{{ route('cooperation.my-account.cooperations.index', ['cooperation' => $cooperation->slug]) }}">--}}
{{--                        @lang('my-account.cooperations.form.header')--}}
{{--                    </a>--}}
{{--                </li>--}}
                <li>
                    @include('cooperation.frontend.shared.parts.logout')
                </li>
                <li>
                    <span class="float-right" style="padding-right:.5em;line-height:100%;">
                        <small>
                            v{{ config('app.version') }}@if(App::environment() != 'production') - {{ App::environment() }}@endif
                        </small>
                    </span>
                </li>
            @endcomponent
        @endif
    </div>
</div>