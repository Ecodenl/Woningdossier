<div class="panel">
    <div class="panel-body panel-chat-body">
        @component('cooperation.layouts.chat.messages')
            @forelse($publicMessages as $publicMessage)

                <li class="@if($publicMessage->isMyMessage()) right @else left @endif clearfix">
                    <div class="chat-body clearfix">
                        <div class="header">
                            @if($publicMessage->isMyMessage())

                                <small class="text-muted">
                                    <span class="glyphicon glyphicon-time"></span>{{$publicMessage->created_at->diffForHumans()}}
                                </small>
                                <strong class="pull-right primary-font">{{$publicMessage->getSender()}}</strong>

                            @else

                                <strong class="primary-font">{{$publicMessage->getSender()}}</strong>
                                <small class="pull-right text-muted">
                                    <span class="glyphicon glyphicon-time"></span>{{$publicMessage->created_at->diffForHumans()}}
                                </small>

                            @endif
                        </div>
                        <p>
                            {{$publicMessage->message}}
                        </p>
                    </div>
                </li>
            @empty

            @endforelse
        @endcomponent
    </div>
    <div class="panel-footer">
        @component('cooperation.layouts.chat.input', ['privateMessages' => $privateMessages, 'buildingId' => $building->id, 'url' => route('cooperation.admin.send-message'), 'isPublic' => true])
            <button type="submit" class="btn btn-primary btn-md" id="btn-chat">
                @lang('woningdossier.cooperation.admin.coach.messages.edit.send')
            </button>
        @endcomponent
    </div>
</div>