@component('cooperation.tool.components.input-group', ['inputType' => 'select', 'inputValues' => $interests, 'userInputValues' => $buildingOwner->userInterestsForSpecificType($interestedInType, $interestedInId)->forMe()->get(), 'userInputColumn' => 'interest_id'])
    <select id="user_interest" class="form-control" name="user_interests[interest_id]">
        @foreach($interests as $interest)
            <option @if(old('user_interests.interest_id', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->userInterestsForSpecificType($interestedInType, $interestedInId), 'interest_id')) == $interest->id) selected="selected" @endif value="{{ $interest->id }}">{{ $interest->name}}</option>
        @endforeach
    </select>
@endcomponent