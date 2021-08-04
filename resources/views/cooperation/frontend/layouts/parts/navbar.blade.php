{{-- Nav bar --}}
<div class="flex flex-wrap flex-row justify-between items-center w-full bg-white h-12 px-5 xl:px-20 relative z-40 shadow-lg">
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
                @component('cooperation.frontend.layouts.components.dropdown', ['label' => __('cooperation/frontend/layouts.navbar.language')])
                    @foreach(config('hoomdossier.supported_locales') as $locale)
                        @if(app()->getLocale() != $locale)
                            <li>
                                <a href="{{ route('cooperation.switch-language', ['cooperation' => $cooperation, 'locale' => $locale]) }}">
                                    @lang('woningdossier.navbar.languages.'. $locale)
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
            @livewire('cooperation.frontend.layouts.parts.messages')
            <div class="flex items-center">
                <i class="icon-md icon-account-circle mr-1"></i>
                <i class="icon-xs icon-arrow-down"></i>
            </div>
        @endif
    </div>
</div>