<div class="panel">
    <div class="panel-body panel-chat-body">
        @component('cooperation.messages.parts.messages')
            <?php
            /**
             * @param \Illuminate\Support\Collection $messages
             * @param \App\Models\PrivateMessage $privateMessage
             */
            ?>
            @foreach($messages as $privateMessage)
                @include('cooperation.messages.parts.message', compact('privateMessage'))
            @endforeach
        @endcomponent
    </div>
</div>
<div class="panel-footer">
    @component('cooperation.messages.parts.input', ['privateMessages' => $messages, 'buildingId' => $building->id, 'url' => route('cooperation.admin.send-message'), 'isPublic' => $isPublic])
        <button type="submit" class="btn btn-primary btn-md" id="btn-chat">
            @lang('woningdossier.cooperation.admin.messages.send')
        </button>
    @endcomponent
</div>