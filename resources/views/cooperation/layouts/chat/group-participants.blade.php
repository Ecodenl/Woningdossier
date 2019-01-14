<div class="group-members">
    @foreach($groupParticipants as $groupParticipant)
        <div class="group-member">
            <span class="label label-primary">
                {{$groupParticipant->getFullName()}}
                @can('remove-participant-from-chat', $groupParticipant)
                    <span data-building-owner-id="{{$buildingId}}" data-user-id="{{$groupParticipant->id}}" class="glyphicon glyphicon-remove"></span>
                @endcan
            </span>
        </div>
    @endforeach
</div>

@push('js')
    <script>
        $(document).ready(function () {
            $('.group-member > span').click(function () {
                // get the user id from the group participant
                var userId = $(this).find('span').data('user-id');
                var buildingOwnerId = $(this).find('span').data('building-owner-id');

                var groupMember = $(this).parent();

                if (confirm('Are you sure')) {

                    $.ajax({
                        url: '{{route('cooperation.my-account.messages.revoke-access')}}',
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
            });
        });
    </script>
@endpush