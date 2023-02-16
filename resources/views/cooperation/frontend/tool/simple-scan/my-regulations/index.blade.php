@extends('cooperation.frontend.layouts.tool')

@section('content')
    @if($activeNotification)
        <livewire:cooperation.frontend.layouts.parts.notifications
                :nextUrl="route('cooperation.frontend.tool.simple-scan.my-regulations.index', compact('scan'))"
                :types="[\App\Jobs\RecalculateStepForUser::class]"
        />
        @include('cooperation.frontend.shared.parts.loader', ['label' => __('cooperation/frontend/tool.my-regulations.loading')])
    @else
        <div class="w-full flex flex-wrap" x-data="{ selected: null }">
            <div class="w-full flex flex-wrap" x-data="tabs(@if(request()->has('tab')) '{{ request()->get('tab') }}' @endif)">
                <nav class="nav-tabs" x-show="selected === null">
                    @foreach(__('cooperation/frontend/tool.my-regulations.categories') as $key => $category)
                        <a x-bind="tab" href="#" @if($loop->first) x-ref="main-tab" @endif data-tab="{{ $key }}">
                            {{ str_replace(':count', count(($relevantRegulations[$key] ?? [])), $category) }}
                        </a>
                    @endforeach
                </nav>

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

        <div class="flex flex-row flex-wrap w-full mb-4">
            <div class="w-full sm:w-1/2">
                <a class="btn btn-green float-left"
                   href="{{ route('cooperation.frontend.tool.simple-scan.my-plan.index', compact('scan')) }}">
                     @lang('cooperation/frontend/tool.my-plan.label')
                </a>
            </div>
        </div>
    @endif
@endsection