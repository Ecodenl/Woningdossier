{{--
    We pass a building id which may be a id that is softdeleted, so now we query and check if its deleted
    if itsnt then we show the form, else we dont. no need for extra convo
--}}

@if(\App\Models\Building::find($buildingId) instanceof \App\Models\Building)
    {{-- Legacy support --}}
    @if(($tailwind ?? false))
        <form action="{{ $url }}" method="post" class="w-full" style="margin-bottom: unset;">
            @csrf
            <input type="hidden" name="building_id" value="{{ $buildingId }}">
            @if(isset($isPublic))
                <input type="hidden" name="is_public" value="{{$isPublic ? 1 : 0}}">
            @endif

            <div class="w-full flex flex-row flex-wrap items-center">
                <input id="btn-input" required autofocus autocomplete="false" name="message" type="text"
                       class="form-input w-10/12 m-0" placeholder="@lang('my-account.messages.edit.chat.input')">

                <span class="w-2/12 pl-3">
                    {{ $slot }}
                </span>
            </div>
        </form>
    @else
        <form action="{{ $url }}" method="post" style="margin-bottom: unset;">
            @csrf
            <div class="input-group">
                <input type="hidden" name="building_id" value="{{ $buildingId }}">

                @if(isset($isPublic))
                    <input type="hidden" name="is_public" value="{{$isPublic ? 1 : 0}}">
                @endif
                <input id="btn-input" required autofocus autocomplete="false" name="message" type="text"
                       class="form-control input-md" placeholder="@lang('my-account.messages.edit.chat.input')">

                <span class="input-group-btn">
                    {{ $slot }}
                </span>

            </div>
        </form>
    @endif
@endif
