<div class="flex">
    @foreach($groupParticipants as $groupParticipant)
        @if($groupParticipant instanceof \App\Models\User)
            <div class="group-member mr-1">
                <span class="px-2 inline text-sm text-blue-500 font-bold bg-green bg-opacity-50 px-1 py-2 rounded-lg flex items-center @cannot('remove-participant-from-chat', $groupParticipant) not-removable-user cursor-not-allowed @endcan @can('remove-participant-from-chat', $groupParticipant) @if(!$groupParticipant->buildings->contains('id', $buildingId)) is-removable-user cursor-pointer @endif @endcan">
                    {{$groupParticipant->getFullName()}}
                    @can('remove-participant-from-chat', $groupParticipant)
                        <span data-building-owner-id="{{$buildingId}}"
                              data-user-id="{{$groupParticipant->id}}"
                              class="icon-sm icon-error-cross ml-2 mr-1"
                        ></span>
                    @endcan
                </span>
            </div>
        @endif
    @endforeach
</div>

@push('js')
    <script type="module">
        document.addEventListener('DOMContentLoaded', function () {
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