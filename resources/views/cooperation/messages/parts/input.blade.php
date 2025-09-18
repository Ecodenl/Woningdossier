{{--
    We pass a building which may be softdeleted, so now we check if its deleted
    if it isn't then we show the form, else we don't. no need for extra convo
--}}

@if($building instanceof \App\Models\Building && ! $building->trashed())
    @php $buildingId = $building->id; @endphp

    <form action="{{ $url }}" method="post" class="w-full" style="margin-bottom: unset;">
        @csrf

        <input type="hidden" name="building_id" value="{{ $buildingId }}">
        @if(isset($isPublic))
            <input type="hidden" name="is_public" value="{{$isPublic ? 1 : 0}}">
        @endif

        <div class="w-full flex flex-row flex-wrap items-center">
            <textarea required autofocus autocomplete="false" name="message" type="text"
                   class="form-input w-10/12 m-0" placeholder="@lang('my-account.messages.edit.chat.input')"
            ></textarea>

            <div class="w-2/12 pl-3 flex justify-center">
                {{ $slot }}
            </div>
        </div>
    </form>
@endif
