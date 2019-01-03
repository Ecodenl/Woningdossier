@isset($typeIds)
    @foreach($typeIds as $typeId)

        <?php
            if($type == "service") {
                $typeName = \App\Models\Service::find($typeId)->name;
            } else {
                $typeName = \App\Models\Element::find($typeId)->name;
            }

            $userInterestsForMe = \App\Models\UserInterest::forMe()->get();
        ?>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group add-space">
                    <label for="interest_{{ $type }}_{{ $typeId }}" class="control-label">
                        {{ \App\Helpers\Translation::translate('general.change-interested.title', ['item' => $typeName]) }}
                    </label>
                    @component('cooperation.tool.components.input-group',
                    ['inputType' => 'select', 'inputValues' => $interests, 'userInputValues' => $userInterestsForMe->where('interested_in_type', $type)->where('interested_in_id', $typeId),  'userInputColumn' => 'interest_id'])
                        <select class="form-control" id="interest_{{ $type }}_{{ $typeId }}" name="interest[{{ $type }}][{{ $typeId }}]">
                            @foreach($interests as $interest)
                                <option @if($interest->id == old('user_interest.'.$type.'.'. $typeId . '')) selected @elseif(Auth::user()->getInterestedType($type, $typeId) != null && Auth::user()->getInterestedType($type, $typeId)->interest_id == $interest->id) selected  @endif value="{{ $interest->id }}">{{ $interest->name }}</option>
                            @endforeach
                        </select>
                    @endcomponent

                    @if ($errors->has('interest.'.$typeId))
                        <span class="help-block">
                            <strong>{{ $errors->first('interest.'.$typeId) }}</strong>
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
@endisset
