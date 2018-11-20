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
                        {{\App\Helpers\Translation::translate('general.change-interested.title', ['item' => $typeName])}}
                    </label>
                    @component('cooperation.tool.components.input-group',
                    ['inputType' => 'select', 'inputValues' => $interests, 'userInputValues' => $userInterestsForMe->where('interested_in_type', $type)->where('interested_in_id', $typeId),  'userInputColumn' => 'interest_id'])
                        <select class="form-control" id="interest_{{ $type }}_{{ $typeId }}" name="interest[{{ $type }}][{{ $typeId }}]">
                            @foreach($interests as $interest)
                                <option data-calculate-value="{{$interest->calculate_value}}" @if($interest->id == old('user_interest.'.$type.'.'. $typeId . '')) selected @elseif(Auth::user()->getInterestedType($type, $typeId) != null && Auth::user()->getInterestedType($type, $typeId)->interest_id == $interest->id) selected  @endif value="{{ $interest->id }}">{{ $interest->name }}</option>
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


    @isset($buildingElement)

    <?php


//        $buildingInsulation = Auth::user()->buildings()->first()->getBuildingElement($buildingElement);
//        $userInterestIdForCurrentType = Auth::user()->getInterestedType($type, $elementId)->interest_id;
//        $interest = \App\Models\Interest::find($userInterestIdForCurrentType);

    ?>

        {{--@foreach($buildingElements->values()->orderBy('order')->get() as $elementValue)--}}
{{--            @if(isset($buildingInsulation->element_value_id) && $elementValue->id == $buildingInsulation->element_value_id)--}}
{{--                @if(($elementValue->calculate_value == 3 || $elementValue->calculate_value == 4) && $interest->calculate_value <= 2)--}}
                    {{-- So we can check on the frontend to hide all the fields --}}
                    {{--<input type="hidden" id="interest_calculate_value" value="{{$interest->calculate_value}}">--}}
                    {{--<input type="hidden" id="{{$type}}_calculate_value" value="{{$elementValue->calculate_value}}">--}}

                    {{--TODO: put in import csv file when all branches are merged. --}}
                    <div id="{{$buildingElement}}-info-alert">
                        @component('cooperation.tool.components.alert', ['type' => 'info', 'hide' => true])
                            Hoe veel u met deze maatregel kunt besparen hangt ervan wat de isolatiewaarde van de huidige isolatielaag is.
                            Voor het uitrekenen van de daadwerkelijke besparing bij het na- isoleren van een reeds geïsoleerde gevel/vloer/dak is aanvullend en gespecialiseerd advies nodig.
                        @endcomponent
                    </div>
                {{--@endif--}}
            {{--@break--}}
            {{--@endif--}}
        {{--@endforeach--}}

    @endisset
@endisset
