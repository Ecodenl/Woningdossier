{{-- Nav bar --}}
<div class="flex flex-wrap flex-row justify-between items-center w-full bg-white h-12 px-5 xl:px-20 relative z-100 shadow-lg">
    <div class="flex flex-row flex-wrap justify-between items-center space-x-4">
        {{-- TODO: Check if this should be interchangable per cooperation --}}
        <a href="{{ route('cooperation.welcome') }}">
            <i class="icon-hoomdossier"></i>
        </a>
        @auth
            @if (Auth::check() && Hoomdossier::user()->isFillingToolForOtherBuilding())
                <a href="{{route('cooperation.admin.stop-session')}}" class="btn btn-yellow">
                    @lang('cooperation/frontend/layouts.navbar.stop-session')
                </a>
            @endif
            {{-- only show the "back to cooperation button when the user is an admin without resident role AND we're on the settings page AND there's only one --}}
            @if(Auth::check() && Hoomdossier::user()->can('access-admin') && ! Hoomdossier::user()->hasRole('resident') && Hoomdossier::user()->getRoleNames()->count() <= 1 && ! Hoomdossier::user()->isFillingToolForOtherBuilding())
                <a href="{{ route('cooperation.admin.index') }}" class="btn btn-green">
                    @lang('cooperation/frontend/layouts.navbar.back-to-cooperation')
                </a>
            @endif

            @if(Hoomdossier::user()->isFillingToolForOtherBuilding())
                <p class="btn btn-purple">Woning: {{$building->user->getFullName()}} - {{"{$building->postal_code} - {$building->number} {$building->extension}"}}</p>
            @endif
        @endauth
        @if(App::environment() == 'local') {{-- currently only for local development --}}
            @if(count(config('hoomdossier.supported_locales')) > 1)
                @component('cooperation.frontend.layouts.components.dropdown', [
                    'label' => __('default.language'),
                    'class' => 'in-text',
                ])
                    @foreach(config('hoomdossier.supported_locales') as $locale)
                        @if(app()->getLocale() != $locale)
                            <li>
                                <a href="{{ route('cooperation.switch-language', ['cooperation' => $cooperation, 'locale' => $locale]) }}"
                                   class="in-text">
                                    @lang('default.languages.'. $locale)
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endcomponent
            @endif
            @auth
                @component('cooperation.frontend.layouts.components.dropdown', [
                    'label' => __('cooperation/frontend/layouts.navbar.input-source'),
                    'class' => 'in-text',
                ])
                    @foreach($inputSources as $inputSource)
                        @if(\App\Models\BuildingFeature::withoutGlobalScope(\App\Scopes\GetValueScope::class)->where('input_source_id', $inputSource->id)->first() instanceof \App\Models\BuildingFeature)
                            <li>
                                <a href="{{ route('cooperation.input-source.change-input-source-value', ['cooperation' => $cooperation, 'input_source_value_id' => $inputSource->id]) }}"
                                   class="in-text">
                                    {{$inputSource->name}}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endcomponent
            @endauth
        @endif
    </div>
    <div class="flex flex-row justify-end items-center space-x-4">
        @if(Auth::check() && Hoomdossier::user()->hasRoleAndIsCurrentRole('resident'))
            @if (! Hoomdossier::user()->isFillingToolForOtherBuilding())
                <p>
                    <a href="{{ route('cooperation.home') }}">
                        @lang('default.start')
                    </a>
                </p>
            @endif
{{--            <p>--}}
{{--                <a class="text-blue" href="{{ route('cooperation.tool.ventilation.index') }}">--}}
{{--                    @lang('cooperation/frontend/layouts.navbar.advise')--}}
{{--                </a>--}}
{{--            </p>--}}
        @endif

        @if (Auth::check() && ! Hoomdossier::user()->isFillingToolForOtherBuilding())
            @if(Hoomdossier::user()->getRoleNames()->count() > 1 && \App\Helpers\HoomdossierSession::hasRole())
                @component('cooperation.frontend.layouts.components.dropdown', [
                    'label' => __('cooperation/frontend/layouts.navbar.current-role') . \Spatie\Permission\Models\Role::find(\App\Helpers\HoomdossierSession::getRole())->human_readable_name,
                    'class' => 'in-text',
                ])
                    @foreach(Hoomdossier::user()->roles()->orderBy('level', 'DESC')->get() as $role)
                        <li>
                            <a href="{{ route('cooperation.admin.switch-role', ['role' => $role->name]) }}"
                               class="in-text">
                                {{ $role->human_readable_name }}
                            </a>
                        </li>
                    @endforeach
                @endcomponent
            @endif

            @livewire('cooperation.frontend.layouts.parts.alerts', ['building' => $building, 'inputSource' => $masterInputSource])
            @livewire('cooperation.frontend.layouts.parts.messages')

            {{-- Keep local for ease of use --}}
            @if(app()->isLocal())
                @if($building instanceof \App\Models\Building && $building->hasCompletedQuickScan($masterInputSource))
                    @component('cooperation.frontend.layouts.components.dropdown', ['label' => '<i class="icon-md icon-check-circle"></i>'])
                        {{-- Loaded in NavbarComposer --}}
                        @foreach($expertSteps as $expertStep)
                            @if($expertStep->short !== 'heating')
                            <li>
                                <a href="{{ route("cooperation.tool.{$expertStep->short}.index", compact('cooperation')) }}"
                                   class="in-text">
                                    <img src="{{ asset("images/icons/{$expertStep->slug}.png") }}"
                                         alt="{{ $expertStep->name }}" class="rounded-1/2 inline-block h-8 w-8">
                                    {{ $expertStep->name }}
                                </a>
                            </li>
                            @endif
                        @endforeach
                    @endcomponent
                @endif
            @endif



            @component('cooperation.frontend.layouts.components.dropdown', ['label' => '<i class="icon-md icon-account-circle"></i>'])
                <li>
                    <a href="{{ route('cooperation.my-account.index', compact('cooperation')) }}"
                       class="in-text">
                        @lang('woningdossier.cooperation.navbar.my-account')
                    </a>
                </li>
                <li>
                    <a href="{{ route('cooperation.privacy.index', compact('cooperation')) }}"
                       class="in-text">
                        @lang('woningdossier.cooperation.navbar.privacy')
                    </a>
                </li>
                <li>
                    <a href="{{ route('cooperation.disclaimer.index', compact('cooperation')) }}"
                       class="in-text">
                        @lang('woningdossier.cooperation.navbar.disclaimer')
                    </a>
                </li>
                <li>
                    <a href="{{ route('cooperation.frontend.help.index', compact('cooperation')) }}"
                       class="in-text">
                        @lang('woningdossier.cooperation.navbar.help')
                    </a>
                </li>
{{--                <li>--}}
{{--                    <a href="{{ route('cooperation.my-account.cooperations.index', ['cooperation' => $cooperation->slug]) }}"--}}
{{--                       class="in-text">--}}
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