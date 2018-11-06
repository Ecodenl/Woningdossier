@isset($typeIds)
    @foreach($typeIds as $elementId)

        <?php
            if($type == "service") {
                $typeName = \App\Models\Service::find($elementId)->name;
            } else {
                $typeName = \App\Models\Element::find($elementId)->name;
            }
        ?>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group add-space">
                    <label for="interest_{{ $type }}_{{ $elementId }}" class="control-label">
                        @lang('woningdossier.cooperation.tool.change-interest', ['item' => $typeName])
                    </label>
                    <select class="form-control" id="interest_{{ $type }}_{{ $elementId }}" name="interest[{{ $type }}][{{ $elementId }}]">
                        @foreach($interests as $interest)
                            <option @if($interest->id == old('user_interest.'.$type.'.'. $elementId . '')) selected @elseif(Auth::user()->getInterestedType($type, $elementId) != null && Auth::user()->getInterestedType($type, $elementId)->interest_id == $interest->id) selected  @endif value="{{ $interest->id }}">{{ $interest->name }}</option>
                        @endforeach
                    </select>

                    @if ($errors->has('interest.'.$elementId))
                        <span class="help-block">
                            <strong>{{ $errors->first('interest.'.$elementId) }}</strong>
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
    <?php $buildingElement = 'floor-insulation'; ?>
    @isset($buildingElement)

        <?php
            $buildingInsulation = Auth::user()->buildings()->first()->getBuildingElement($buildingElement);
            $userInterestIdForCurrentType = Auth::user()->getInterestedType($type, $elementId)->interest_id;
            $interest = \App\Models\Interest::find($userInterestIdForCurrentType);

        ?>

        @foreach($buildingElements->values()->orderBy('order')->get() as $elementValue)
{{--            {{dd($buildingInsulation->element_value_id, $elementValue, $buildingInsulation)}}--}}
            @if(isset($buildingInsulation->element_value_id) && $elementValue->id == $buildingInsulation->element_value_id)
                @if(($elementValue->calculate_value == 3 || $elementValue->calculate_value == 4) && $interest->calculate_value <= 2)
                    {{--TODO: put in import csv file when all branches are merged. --}}
                    @component('cooperation.tool.components.alert', ['type' => 'info'])
                        Hoe veel u met deze maatregel kunt besparen hangt ervan wat de isolatiewaarde van de huidige isolatielaag is.
                        Voor het uitrekenen van de daadwerkelijke besparing bij het na- isoleren van een reeds geïsoleerde gevel/vloer/dak is aanvullend en gespecialiseerd advies nodig.
                    @endcomponent
                @endif
            @break
            @endif
        @endforeach

    @endisset
@endisset
