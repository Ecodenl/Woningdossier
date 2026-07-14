@extends('cooperation.frontend.layouts.tool')

@section('content')
    @if($activeNotification)
        <livewire:cooperation.frontend.layouts.parts.notifications
                :types="[\App\Jobs\RecalculateStepForUser::class]"
                :nextUrl="route('cooperation.frontend.tool.simple-scan.my-plan.index', compact('scan'))"
        />
        @include('cooperation.frontend.shared.parts.loader', ['label' => __('cooperation/frontend/tool.my-plan.loading')])
    @else
        <div class="w-full">
            @php
                $smartTwinError = in_array(request('smarttwin_error'), ['not_configured', 'advice_in_progress', 'failed'], true)
                    ? request('smarttwin_error')
                    : null;
            @endphp
            @if($smartTwinError)
                @component('cooperation.layouts.components.alert', ['color' => 'red', 'withBackground' => true])
                    {{ __("cooperation/frontend/tool.my-plan.smarttwin.errors.{$smartTwinError}") }}
                @endcomponent
            @endif
            @php
                $langShort = $building->hasAnsweredExpertQuestion() ? 'expert' : 'quick-scan';
                $link = route('cooperation.my-account.messages.edit', compact('cooperation'));

                if (Hoomdossier::user()->can('access-admin') && Hoomdossier::user()->hasRoleAndIsCurrentRole(['coordinator', 'coach', 'cooperation-admin'])) {
                    $link = route('cooperation.admin.messages.index', compact('cooperation'));
                }
            @endphp
            <div class="w-full flex flex-wrap justify-between mb-5">
                <h4 class="heading-4">
                    {!! __("cooperation/frontend/tool.my-plan.title.{$langShort}") !!}
                </h4>
            </div>
            <div class="w-full flex flex-wrap mb-5">
                {!! __("cooperation/frontend/tool.my-plan.info.{$langShort}", compact('link')) !!}
            </div>

            <livewire:cooperation.frontend.tool.simple-scan.my-plan.form :scan="$scan" :building="$building"/>
            <livewire:cooperation.frontend.tool.simple-scan.my-plan.comments :scan="$scan" :building="$building"/>
        </div>

        <div class="w-full flex flex-wrap pt-5 pb-5">
            <div class="flex w-1/4">
                @can('viewAny', [\App\Models\Media::class, $inputSource, $building])
                    <a href="{{ route('cooperation.frontend.tool.simple-scan.my-plan.media', compact('cooperation', 'scan')) }}"
                       class="btn btn-outline-green">
                        @lang('cooperation/frontend/tool.my-plan.uploader.view')
                    </a>
                @endcan
            </div>
            <livewire:cooperation.frontend.tool.simple-scan.my-plan.calculations-table :building="$building"/>
            <livewire:cooperation.frontend.tool.simple-scan.my-plan.download-pdf :user="$building->user" :scan="$scan"/>
        </div>

        @if(Hoomdossier::hasEnabledSmartTwinCalls() && ! empty(Hoomdossier::user()->extra['smarttwin_user_id'] ?? null))
            <div class="w-full flex flex-wrap pb-5">
                <form method="POST"
                      action="{{ route('cooperation.frontend.tool.simple-scan.my-plan.smarttwin', compact('cooperation', 'scan')) }}">
                    @csrf
                    <button type="submit" class="btn btn-green">
                        @lang('cooperation/frontend/tool.my-plan.smarttwin.button')
                    </button>
                </form>
            </div>
        @endif
    @endif
@endsection