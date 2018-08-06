<div class="col-md-2">
    <ul class="nav nav-pills nav-stacked">
        <li @if(Route::currentRouteName() == 'cooperation.my-account.messages.index') class="active" @endif>
            <a href="{{route('cooperation.my-account.messages.index', ['cooperation' => $cooperation])}}">
                @lang('woningdossier.cooperation.my-account.messages.navigation.inbox')
                <span class="pull-right badge">{{$myUnreadMessages->count()}}</span>
            </a>
        </li>
        <li  @if(in_array(Route::currentRouteName(), ['cooperation.my-account.messages.requests.index', 'cooperation.my-account.messages.requests.edit']))) class="active" @endif >
            <a href="{{route('cooperation.my-account.messages.requests.index')}}">@lang('woningdossier.cooperation.my-account.messages.navigation.requests')</a>
        </li>
        {{--@if(App\Models\PrivateMessage::hasUserResponseToConversationRequest())--}}

        {{--@elseif(isset($coachConversationRequest))--}}
            {{--<li ><a href="{{route('cooperation.conversation-requests.coach.index', ['cooperation' => $cooperation])}}">@lang('woningdossier.cooperation.my-account.messages.navigation.conversation-requests.update-request')</a></li>--}}
        {{--@else--}}
            {{--<li ><a href="{{route('cooperation.conversation-requests.coach.index', ['cooperation' => $cooperation])}}">@lang('woningdossier.cooperation.my-account.messages.navigation.conversation-requests.request')</a></li>--}}
        {{--@endif--}}

    </ul>
</div>