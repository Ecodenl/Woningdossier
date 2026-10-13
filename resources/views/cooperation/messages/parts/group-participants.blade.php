{{-- Read-only on purpose: removing a coach here was too easy to do by accident. That lives at the
     bottom of 'My account' now. --}}
<div class="flex">
    @foreach($groupParticipants as $groupParticipant)
        @if($groupParticipant instanceof \App\Models\User)
            <div class="group-member mr-1">
                <span class="px-2 inline text-sm text-blue font-bold bg-green bg-opacity-50 py-2 rounded-lg items-center">
                    {{$groupParticipant->getFullName()}}
                </span>
            </div>
        @endif
    @endforeach
</div>
