@extends('cooperation.frontend.layouts.tool')

@section('content')
    @php
        $messageBox = [
            'privateMessages' => $privateMessages,
            'building' => $building,
            'isPublic' => true,
            'showParticipants' => true,
            'url' => route('cooperation.my-account.messages.store'),
        ];
    @endphp

    @if(Hoomdossier::hasEnabledSmartTwinCalls())
        {{-- The coach tile from the dashboard beside the conversation. No contact button: this is
             where it would lead. --}}
        <div class="messages-with-coach">
            @include('cooperation.frontend.dashboard.parts.coach', [
                'coach' => $coach,
                'appointmentDate' => $appointmentDate,
                'withContact' => false,
            ])

            <div class="messages-with-coach-conversation">
                @include('cooperation.layouts.parts.message-box', $messageBox)
            </div>
        </div>
    @else
        <div class="flex flex-row flex-wrap w-full border border-solid border-blue-500 border-opacity-50 rounded-lg">
            @include('cooperation.layouts.parts.message-box', $messageBox)
        </div>
    @endif
@endsection
