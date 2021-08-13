<div class="flex flex-row flex-wrap w-full">
    <div class="w-full">
        @component('cooperation.tool.components.step-question', [
            'id' => 'user_interest', 'translation' => $translation,
        ])
            @slot('sourceSlot')
                @include('cooperation.tool.components.source-list', [
                    'inputType' => 'select', 'inputValues' => $interests,
                    'userInputValues' => $buildingOwner->userInterestsForSpecificType($interestedInType, $interestedInId)->forMe()->get(),
                    'userInputColumn' => 'interest_id',
                ])
            @endslot
            @component('cooperation.frontend.layouts.components.alpine-select')
                <select id="user_interest" class="form-input" name="user_interests[interest_id]">
                    @foreach($interests as $interest)
                        <option value="{{ $interest->id }}"
                                @if(old('user_interests.interest_id', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->userInterestsForSpecificType($interestedInType, $interestedInId), 'interest_id')) == $interest->id) selected="selected" @endif>
                            {{ $interest->name}}
                        </option>
                    @endforeach
                </select>
            @endcomponent

            <input type="hidden" name="user_interests[interested_in_type]" value="{{$interestedInType}}">
            <input type="hidden" name="user_interests[interested_in_id]" value="{{$interestedInId}}">
        @endcomponent
    </div>
</div>
