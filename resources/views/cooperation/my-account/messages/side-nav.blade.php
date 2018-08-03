<div class="col-md-2">
    <ul class="nav nav-pills nav-stacked">
        <li class="active">
            <a href="{{route('cooperation.my-account.messages.index', ['cooperation' => $cooperation])}}">
                @lang('woningdossier.cooperation.my-account.messages.navigation.inbox')
                <span class="pull-right badge">{{$myUnreadMessages->count()}}</span>
            </a>
        </li>
        @if(App\Models\PrivateMessage::hasUserResponseToCoachConversationRequest())

        @elseif(isset($coachConversationRequest))
            <li ><a href="{{route('cooperation.coach-conversation-request.index', ['cooperation' => $cooperation])}}">@lang('woningdossier.cooperation.my-account.messages.navigation.coach-conversation.update-request')</a></li>
        @else
            <li ><a href="{{route('cooperation.coach-conversation-request.index', ['cooperation' => $cooperation])}}">@lang('woningdossier.cooperation.my-account.messages.navigation.coach-conversation.request')</a></li>
        @endif

    </ul>
</div>