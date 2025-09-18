@extends('cooperation.frontend.layouts.tool')

@section('content')
    <div class="flex flex-row flex-wrap w-full border border-solid border-blue-500 border-opacity-50 rounded-lg">
        @include('cooperation.layouts.parts.message-box', [
            'privateMessages' => $privateMessages,
            'building' => $building,
            'isPublic' => true,
            'showParticipants' => true,
            'url' => route('cooperation.my-account.messages.store'),
        ])
    </div>
@endsection