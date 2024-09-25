<div>
    <div class="w-full flex flex-wrap" x-data="{ selected: null }">

        <div class="w-full flex flex-wrap justify-between" x-data="tabs(@if(request()->has('tab')) '{{ request()->get('tab') }}' @endif)">
            <nav class="nav-tabs" x-show="selected === null">
                @foreach(__('cooperation/frontend/tool.my-regulations.categories') as $key => $category)
                    <a x-bind="tab" href="#" @if($loop->first) x-ref="main-tab" @endif data-tab="{{ $key }}">
                        {{ str_replace(':count', count(($relevantRegulations[$key] ?? [])), $category) }}
                    </a>
                @endforeach
            </nav>
            <div class="flex items-center gap-x-2" @if($isRefreshing) wire:poll="checkIfIsRefreshed" @endif>
                <button class="btn btn-purple" type="button" @if($isRefreshing) disabled="disabled" @endif wire:click="refreshRegulations">
                    <span class="w-full mx-1 flex justify-between items-center">
                        @if($isRefreshing)
                            <i class="icon-md icon-ventilation-fan animate-spin-slow"></i>
                            @lang('cooperation/frontend/tool.my-regulations.refreshed.busy')
                        @else
                            @lang('cooperation/frontend/tool.my-regulations.refreshed.ready')
                        @endif
                    </span>
                </button>
                @if($building->user->regulations_refreshed_at instanceof DateTime)
                    <h6 class="heading-6 text-purple">@lang('cooperation/frontend/tool.my-regulations.refreshed.last', ['date' => $building->user->regulations_refreshed_at->format('Y-m-d H:i')])</h6>
                @else
                    <h6 class="heading-6 text-purple">@lang('cooperation/frontend/tool.my-regulations.refreshed.not')</h6>
                @endif
            </div>

            <div class="w-full border border-blue-500 rounded-r rounded-bl p-4">
                @foreach($relevantRegulations as $regulationType => $relevantRegulationsForType)
                    <div x-bind="container" data-tab="{{ $regulationType }}">
                        <p x-show="selected === null">
                            @lang("cooperation/frontend/tool.my-regulations.container.intro.{$regulationType}")
                        </p>

                        @foreach($relevantRegulationsForType as $regulation)
                            <div class="regulation-card-wrapper"
                                 x-show="selected === null || selected === '{{ $regulation['Id'] }}'">
                                <div id="result-{{$regulation['Id']}}"
                                     class="regulation-card relative ease-out duration-300"
                                     x-bind:class="selected === null ? 'cursor-pointer border-transparent hover:border-blue-500' : 'border-blue-500'"
                                     x-on:click="selected = '{{ $regulation['Id'] }}'">
                                    <i class="icon-md icon-close-circle-light clickable absolute top-4 right-4"
                                       x-show="selected === '{{ $regulation['Id'] }}'"
                                       x-on:click.stop="selected = null"></i>
                                    <h4 class="heading-4">
                                        {{ $regulation['Title'] }}
                                    </h4>
                                    <p>
                                        {{ $regulation['Intro'] }}
                                    </p>
                                    <div class="my-8"></div>
                                    <div class="flex flex-wrap flex-row w-full items-center">
                                                <span class="flex as-text mr-1">
                                                    {{ Str::ucfirst(__('default.for')) }}:
                                                </span>
                                        {{-- TODO: Logic --}}
                                        @php($total = count($regulation['advisable_names']))
                                        @for($i = 0; $i < $total; $i++)
                                            <span class="flex as-text bubble"
                                                  @if($i > 2) x-show="selected === '{{ $regulation['Id'] }}'" @endif>
                                                {{$regulation['advisable_names'][$i]}}
                                            </span>
                                        @endfor
                                        @if($total > 3)
                                            <span class="flex as-text bubble" x-show="selected === null">
                                                +{{ $total - 3 }}
                                            </span>
                                        @endif
                                    </div>
                                    @if(App::isLocal())
                                        <div class="flex flex-wrap flex-row w-full items-center">
                                            <span class="flex as-text mr-1">
                                                Verbeterjehuis maatregelen:
                                            </span>

                                            @foreach($regulation['Tags'] as $tag)
                                                <span class="flex as-text bubble">
                                                    {{$tag['Label']}}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <div x-show="selected === '{{ $regulation['Id'] }}'" class="detail-wrapper">
                                    {!! (new \App\Helpers\Sanitizers\HtmlSanitizer())->sanitize($regulation['Details']) !!}

                                    @if(! empty($regulation['ProviderUrl']))
                                        <a target="_blank" rel="nofollow" href="{{ $regulation['ProviderUrl'] }}"
                                           class="btn btn-green">
                                            @lang('cooperation/frontend/tool.my-regulations.provider.to')
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
