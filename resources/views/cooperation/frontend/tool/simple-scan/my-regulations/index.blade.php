@extends('cooperation.frontend.layouts.tool')

@section('content')
    @if($activeNotification)
        <livewire:cooperation.frontend.layouts.parts.notifications
                :nextUrl="route('cooperation.frontend.tool.simple-scan.my-plan.index', compact('scan'))"
                :types="[\App\Jobs\RecalculateStepForUser::class]"
        />
        @include('cooperation.frontend.shared.parts.loader', ['label' => __('cooperation/frontend/tool.my-plan.loading')])
    @else
        <div class="w-full flex flex-wrap pt-5 pb-5" x-data="tabs()">
            <nav class="nav-tabs">
                @foreach(__('cooperation/frontend/tool.my-regulations.categories') as $key => $category)
                    <a x-bind="tab" href="#" @if($loop->first) x-ref="main-tab" @endif data-tab="{{ $key }}">
                        {{ str_replace(':count', 0, $category) }}
                    </a>
                @endforeach
            </nav>

            <div class="w-full border border-blue-500 rounded p-4">
                @foreach(__('cooperation/frontend/tool.my-regulations.categories') as $key => $category)
                    <div x-bind="container" data-tab="{{ $key }}">
                        <p>
                            @lang("cooperation/frontend/tool.my-regulations.container.intro.{$key}")
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endsection