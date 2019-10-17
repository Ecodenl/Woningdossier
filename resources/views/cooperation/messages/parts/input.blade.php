{{--
    We pass a building id which may be a id that is softdeleted, so now we query and check if its deleted
    if itsnt then we show the form, else we dont. no need for extra convo
--}}

@if(\App\Models\Building::find($buildingId) instanceof \App\Models\Building)
    <form action="{{ $url }}" method="post" style="margin-bottom: unset;">
        {{ csrf_field() }}
        <div class="input-group">
            <input type="hidden" name="building_id" value="{{ $buildingId }}">

            @if(isset($isPublic))
                <input type="hidden" name="is_public" value="{{$isPublic ? 1 : 0}}">
            @endif
            <input id="btn-input" required autofocus autocomplete="false" name="message" type="text" class="form-control input-md" placeholder="@lang('my-account.messages.edit.chat.input')" />

            <span class="input-group-btn">
                {{ $slot }}
            </span>

        </div>
    </form>
@endif
