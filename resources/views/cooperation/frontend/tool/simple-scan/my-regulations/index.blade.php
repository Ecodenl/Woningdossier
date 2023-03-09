@extends('cooperation.frontend.layouts.tool')

@section('content')
    @if($activeNotification)
        <livewire:cooperation.frontend.layouts.parts.notifications
                :nextUrl="route('cooperation.frontend.tool.simple-scan.my-regulations.index', compact('scan'))"
                :types="[\App\Jobs\RecalculateStepForUser::class]"
        />
        @include('cooperation.frontend.shared.parts.loader', ['label' => __('cooperation/frontend/tool.my-regulations.loading')])
    @else
        {{-- Wrapping div to "ignore" the layout's space-y-20 --}}
        <div>
            <livewire:cooperation.frontend.tool.simple-scan.my-regulations :building="$building"/>

            <div class="flex flex-row flex-wrap w-full my-4">
                <div class="w-full sm:w-1/2">
                    <a class="btn btn-green float-left"
                       href="{{ route('cooperation.frontend.tool.simple-scan.my-plan.index', compact('scan')) }}">
                         @lang('cooperation/frontend/tool.my-plan.label')
                    </a>
                </div>
            </div>
        </div>
    @endif
@endsection