@extends('cooperation.frontend.layouts.tool')

@section('content')
    @if($notification instanceof \App\Models\Notification)
        @livewire('cooperation.frontend.layouts.parts.notifications', [
            'nextUrl' => route('cooperation.frontend.tool.quick-scan.my-plan.index'),
            'type' => 'recalculate'
        ])

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

            @livewire('cooperation.frontend.tool.quick-scan.my-plan.form', compact('building'))
            @livewire('cooperation.frontend.tool.quick-scan.my-plan.comments', compact('building'))

        </div>

        @livewire('cooperation.frontend.tool.quick-scan.my-plan.download-pdf', ['user' => $building->user])
    @endif
@endsection