<div class="row">
    <div class="col-sm-12">
        @component('cooperation.tool.components.step-question', ['id' => 'user_interest', 'translation' => $translation])
            @include('cooperation.tool.parts.user-interest-select', compact('interestedInType', 'interestedInId'))
            <input type="hidden" name="user_interests[interested_in_type]" value="{{$interestedInType}}">
            <input type="hidden" name="user_interests[interested_in_id]" value="{{$interestedInId}}">
        @endcomponent
    </div>
</div>
