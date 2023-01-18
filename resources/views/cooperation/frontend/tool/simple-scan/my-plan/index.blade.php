@extends('cooperation.frontend.layouts.tool')

@section('content')
    @if($activeNotification)
        <livewire:cooperation.frontend.layouts.parts.notifications
                :nextUrl="route('cooperation.frontend.tool.simple-scan.my-plan.index', compact('scan'))"
                :types="[\App\Jobs\RecalculateStepForUser::class]"
        />
        @include('cooperation.frontend.shared.parts.loader', ['label' => __('cooperation/frontend/tool.my-plan.loading')])
    @else
        <div class="w-full">
            @php $langShort = $building->hasAnsweredExpertQuestion() ? 'expert' : 'quick-scan'; @endphp
            <div class="w-full flex flex-wrap justify-between mb-5">
                <h4 class="heading-4">
                    {!! __("cooperation/frontend/tool.my-plan.title.{$langShort}") !!}
                </h4>
                <p>
                    @lang('cooperation/frontend/tool.my-plan.help')
                </p>
            </div>
            <div class="w-full flex flex-wrap mb-5">
                {!! __("cooperation/frontend/tool.my-plan.info.{$langShort}", [
                    'link' => route('cooperation.conversation-requests.index', ['cooperation' => $cooperation, 'requestType' => \App\Services\PrivateMessageService::REQUEST_TYPE_COACH_CONVERSATION])
                ]) !!}
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
    @endif
@endsection