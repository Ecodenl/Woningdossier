<form action="{{ $url }}" method="post" style="margin-bottom: unset;">
    {{ csrf_field() }}
    <div class="input-group">
        <input type="hidden" name="building_id" value="{{ $buildingId }}">

        <input id="btn-input" required autofocus autocomplete="false" name="message" type="text" class="form-control input-md" placeholder="@lang('woningdossier.cooperation.my-account.messages.edit.chat.input')" />

        <span class="input-group-btn">
            {{ $slot }}
        </span>

    </div>
</form>
