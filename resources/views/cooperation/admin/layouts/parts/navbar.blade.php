{{-- Nav bar --}}
<div class="flex flex-wrap flex-row justify-between items-center w-full bg-white h-12 px-5 xl:px-8 relative z-150 shadow-lg">
    @php $building = \App\Helpers\HoomdossierSession::getBuilding(true); @endphp
    <div class="flex flex-row flex-wrap justify-between items-center space-x-4">
        <a href="{{ route('cooperation.admin.index') }}">
            <i class="icon-hoomdossier"></i>
        </a>

        @if(App::environment() == 'local') {{-- currently only for local development --}}
            @if(count(config('hoomdossier.supported_locales')) > 1)
                @component('cooperation.layouts.components.dropdown', [
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
                @component('cooperation.layouts.components.dropdown', [
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
        @can('viewAny', \App\Models\PrivateMessage::class)
            <livewire:cooperation.frontend.layouts.parts.messages/>
        @endif

        @if(Hoomdossier::user()->hasRoleAndIsCurrentRole(['super-admin','superuser']))
            <div>
                <a href="{{ route('cooperation.admin.example-buildings.index') }}" class="in-text">
                    @lang('woningdossier.cooperation.admin.navbar.example-buildings')
                </a>
            </div>
        @endif

        @include('cooperation.layouts.parts.role-switcher')

        @component('cooperation.layouts.components.dropdown', [
            'label' => Hoomdossier::user()->first_name . ' ' . \App\Helpers\Hoomdossier::user()->last_name,
            'class' => 'in-text',
        ])
            <li>
                <a href="{{ route('cooperation.my-account.index', compact('cooperation')) }}"
                   class="in-text">
                    @lang('woningdossier.cooperation.navbar.my-account')
                </a>

                @if(app()->isLocal())
                    <p>
                        B: {{ $building->id }}
                        <br>
                        U: {{ $building->user->id }}
                    </p>
                @endif
            </li>
            <li>
                <a href="{{ route('cooperation.my-account.two-factor-authentication.index', compact('cooperation')) }}"
                   class="in-text">
                    @lang('woningdossier.cooperation.navbar.two-factor-authentication')
                </a>
            </li>
            <li>
                @include('cooperation.layouts.parts.logout')
            </li>
            <li>
                    <span class="float-right" style="padding-right:.5em;line-height:100%;">
                        <small>
                            v{{ config('app.version') }}@if(App::environment() != 'production') - {{ App::environment() }}@endif
                        </small>
                    </span>
            </li>
        @endcomponent
    </div>
</div>