@extends('cooperation.frontend.layouts.tool')

@section('content')
    @if($activeNotification)
        <livewire:cooperation.frontend.layouts.parts.notifications
                :nextUrl="route('cooperation.frontend.tool.simple-scan.my-plan.index', compact('scan'))"
                :types="[\App\Jobs\RecalculateStepForUser::class]"
        />
        @include('cooperation.frontend.shared.parts.loader', ['label' => __('cooperation/frontend/tool.my-plan.loading')])
    @else
        <div class="w-full flex flex-wrap" x-data="{ selected: null }">
            <div class="w-full flex flex-wrap" x-data="tabs()">
                <nav class="nav-tabs" x-show="selected === null">
                    @foreach(__('cooperation/frontend/tool.my-regulations.categories') as $key => $category)
                        <a x-bind="tab" href="#" @if($loop->first) x-ref="main-tab" @endif data-tab="{{ $key }}">
                            {{ str_replace(':count', count(($payload[$key] ?? [])), $category) }}
                        </a>
                    @endforeach
                </nav>

                <div class="w-full border border-blue-500 rounded-r rounded-bl p-4">
                    @foreach(__('cooperation/frontend/tool.my-regulations.categories') as $key => $category)
                        <div x-bind="container" data-tab="{{ $key }}">
                            <p x-show="selected === null">
                                @lang("cooperation/frontend/tool.my-regulations.container.intro.{$key}")
                            </p>

                            @foreach(($payload[$key] ?? []) as $result)
                                <div class="regulation-card-wrapper"
                                     x-show="selected === null || selected === '{{ $result['Id'] }}'">
                                    <div id="result-{{$result['Id']}}" class="regulation-card relative ease-out duration-300"
                                         x-bind:class="selected === null ? 'cursor-pointer border-transparent hover:border-blue-500' : 'border-blue-500'"
                                         x-on:click="selected = '{{ $result['Id'] }}'">
                                        <i class="icon-md icon-close-circle-light clickable absolute top-4 right-4"
                                           x-show="selected === '{{ $result['Id'] }}'"
                                           x-on:click.stop="selected = null"></i>
                                        <h4 class="heading-4">
                                            {{ $result['Title'] }}
                                        </h4>
                                        <p>
                                            {{ $result['Intro'] }}
                                        </p>
                                        <div class="my-8"></div>
                                        @php
                                            $advisable = $advices->first()->userActionPlanAdvisable()->forInputSource($masterInputSource)->first();
                                            $target = \App\Services\MappingService::init()->from($advisable)->resolveTarget();
                                        @endphp
                                        <div class="flex flex-wrap flex-row w-full items-center">
                                            @if($advices->isNotEmpty())
                                                <span class="flex as-text mr-1">
                                                    {{ Str::ucfirst(__('default.for')) }}:
                                                </span>
                                                {{-- TODO: Logic --}}
                                                @php $total = mt_rand(1, $advices->count()); @endphp
                                                @for($i = 0; $i < $total; $i++)
                                                    <span class="flex as-text bubble" @if($i > 2) x-show="selected === '{{ $result['Id'] }}'" @endif>

                                                        {{-- TODO: currently custom measures are being annoying, but not spending too much time on it due to potential change in logic --}}
                                                        {{ $advices[$i]->userActionPlanAdvisable->name ?? $advices[$i]->userActionPlanAdvisable()->forInputSource($masterInputSource)->first()->name }}
                                                    </span>
                                                @endfor
                                                @if($total > 3)
                                                    <span class="flex as-text bubble" x-show="selected === null">
                                                        +{{ $total - 3 }}
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>

                                    <div x-show="selected === '{{ $result['Id'] }}'" class="my-4">
                                        {!! $result['Details'] !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endsection