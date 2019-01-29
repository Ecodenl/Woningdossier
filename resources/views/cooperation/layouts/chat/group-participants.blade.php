@if(isset($isPublic))
    @if($isPublic)
        <div class="pubic-notification">
            <span class="label label-danger">Deze chat is publiek.</span>
        </div>
    @else
        <div class="pubic-notification">
            <span class="label label-warning">Deze chat is prive.</span>
        </div>
    @endif
@endif
<div class="group-members">
    @foreach($groupParticipants as $groupParticipant)
        @if($groupParticipant instanceof \App\Models\User)
        <div class="group-member">
            <span class="label label-primary @cannot('remove-participant-from-chat', $groupParticipant) not-removable-user @endcan @can('remove-participant-from-chat', $groupParticipant) is-removable-user @endcan">
                {{$groupParticipant->getFullName()}}
                @can('remove-participant-from-chat', $groupParticipant)
                    <span data-building-owner-id="{{$buildingId}}" data-user-id="{{$groupParticipant->id}}" class="glyphicon glyphicon-remove"></span>
                @endcan
            </span>
        </div>
        @endif
    @endforeach
</div>

@push('js')
    <script>
        $(document).ready(function () {
            $('.group-member > span').click(function () {
                if ($(this).hasClass('is-removable-user')) {

                    // get the user id from the group participant
                    var userId = $(this).find('span').data('user-id');
                    var buildingOwnerId = $(this).find('span').data('building-owner-id');

                    var groupMember = $(this).parent();

                    if (confirm('@lang('woningdossier.cooperation.chat.group-participants.revoke-access')')) {

                        $.ajax({
                            url: '{{route('cooperation.messages.participants.revoke-access')}}',
                            method: 'POST',
                            data: {
                                user_id: userId,
                                building_owner_id: buildingOwnerId
                            },
                            success: function (data) {
                                $(groupMember).remove();
                            }
                        });
                    }
                }
            });
        });
    </script>
@endpush