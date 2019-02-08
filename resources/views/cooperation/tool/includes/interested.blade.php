@isset($typeIds)
    @foreach($typeIds as $typeId)

        <?php
            if ('service' == $type) {
                $typeName = \App\Models\Service::find($typeId)->name;
            } else {
                $typeName = \App\Models\Element::find($typeId)->name;
            }

            $userInterestsForMe = \App\Models\UserInterest::forMe()->get();
            $buildingOwner = \App\Models\User::find(\App\Models\Building::find(\App\Helpers\HoomdossierSession::getBuilding())->user_id);
        ?>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group add-space">
                    <label for="interest_{{ $type }}_{{ $typeId }}" class="control-label">
                        <i data-toggle="collapse" data-target="#interest_{{ $type }}_{{ $typeId }}_help" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                        {{\App\Helpers\Translation::translate('general.change-interested.title', ['item' => $typeName])}}
                    </label>
                    @component('cooperation.tool.components.input-group',
                    ['inputType' => 'select', 'inputValues' => $interests, 'userInputValues' => $userInterestsForMe->where('interested_in_type', $type)->where('interested_in_id', $typeId),  'userInputColumn' => 'interest_id'])
                        <select class="form-control" id="interest_{{ $type }}_{{ $typeId }}" name="interest[{{ $type }}][{{ $typeId }}]">
                            @foreach($interests as $interest)
                                <option data-calculate-value="{{$interest->calculate_value}}" @if($interest->id == old('user_interest.' . $type . '.'. $typeId, \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->interests()->where('interested_in_type', $type)->where('interested_in_id', $typeId), 'interest_id'))) selected="selected" @endif value="{{ $interest->id }}">{{ $interest->name }}</option>
                                {{--<option data-calculate-value="{{$interest->calculate_value}}" @if($interest->id == old('user_interest.'.$type.'.'. $typeId . '')) selected @elseif(Auth::user()->getInterestedType($type, $typeId) != null && Auth::user()->getInterestedType($type, $typeId)->interest_id == $interest->id) selected  @endif value="{{ $interest->id }}">{{ $interest->name }}</option>--}}
                            @endforeach
                        </select>
                    @endcomponent
                    <div id="interest_{{ $type }}_{{ $typeId }}_help" class="collapse alert alert-info remove-collapse-space alert-top-space">
                        {{\App\Helpers\Translation::translate('general.change-interested.help', ['item' => $typeName])}}
                    </div>
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
