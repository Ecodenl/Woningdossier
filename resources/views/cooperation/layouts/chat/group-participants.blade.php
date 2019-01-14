<div class="group-members">
    @foreach($groupParticipants as $groupParticipant)
        <div class="group-member">
            <span class="label label-primary">
                {{$groupParticipant->getFullName()}}
                @can('remove-participant-from-chat', $groupParticipant)
                    <span class="glyphicon glyphicon-remove"></span>
                @endcan
            </span>
        </div>
    @endforeach
</div>