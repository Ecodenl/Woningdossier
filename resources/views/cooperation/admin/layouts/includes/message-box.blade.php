<div class="panel">
    <div class="panel-body panel-chat-body">
        @component('cooperation.layouts.chat.messages')
            <?php
            /**
             * @param \Illuminate\Support\Collection $messages
             * @param \App\Models\PrivateMessage $privateMessage
             */
            ?>
            @foreach($messages as $privateMessage)

                <li class="@if($privateMessage->isMyMessage()) right @else left @endif clearfix">
                    <div class="chat-body clearfix">
                        <div class="header">
                            @if($privateMessage->isMyMessage())

                                <small class="text-muted">
                                    <span class="glyphicon glyphicon-time"></span>{{$privateMessage->created_at->diffForHumans()}}
                                </small>
                                <strong class="pull-right primary-font">{{$privateMessage->getSender()}}</strong>

                            @else

                                <strong class="primary-font">{{$privateMessage->getSender()}}</strong>
                                <small class="pull-right text-muted">
                                    <span class="glyphicon glyphicon-time"></span>{{$privateMessage->created_at->diffForHumans()}}
                                </small>

                            @endif
                        </div>
                        <p>
                            {{$privateMessage->message}}
                        </p>
                    </div>
                </li>
            @endforeach
        @endcomponent
    </div>
</div>
<div class="panel-footer">
    @component('cooperation.layouts.chat.input', ['privateMessages' => $messages, 'buildingId' => $building->id, 'url' => route('cooperation.admin.send-message'), 'isPublic' => $isPublic])
        <button type="submit" class="btn btn-primary btn-md" id="btn-chat">
            @lang('woningdossier.cooperation.admin.messages.send')
        </button>
    @endcomponent
</div>