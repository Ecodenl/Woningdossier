<?php
    $glazingCount = 0;
    $glazingElements = ['sleeping-rooms-windows', 'living-rooms-windows', 'crack-sealing'];
?>
@foreach($elements as $element)
    <?php
        $buildingElementsOrderedOnCredibility = Hoomdossier::orderRelationShipOnInputSourceCredibility(
            $building->buildingElements()->where('element_id', $element->id)
        )->get();
    ?>
    @if(!in_array($element->short, $glazingElements))
        <div class="row">
    @elseif(in_array($element->short, $glazingElements))
        @if($glazingCount == 0)
            <div class="row">
        @endif
        <?php $glazingCount++ ?>
    @endif
        <div class="col-sm-12 col-md-4 ">
            @if($glazingCount == 1 || !in_array($element->short, $glazingElements))
                <img class="img-responsive mt-15 pr-10 d-inline pull-left" src="{{asset('images/element-icons/'.\App\Helpers\StepHelper::ELEMENT_TO_SHORT[$element->short].'.png')}}">
            @endif
            @component('cooperation.tool.components.step-question', ['id' => 'element.'.$element->id.'.element_value_id', 'translation' => 'cooperation/tool/general-data/current-state.index.element.'.$element->short])
                <input type="hidden" name="elements[{{$element->short}}][element_id]" value="{{$element->id}}">
                @component('cooperation.tool.components.input-group', ['inputType' => 'select', 'inputValues' => $element->values, 'userInputValues' => $building->buildingElements()->forMe()->where('element_id', $element->id)->get(), 'userInputColumn' => 'element_value_id'])
                    <select id="element_{{ $element->id }}" class="form-control" name="elements[{{ $element->short }}][element_value_id]">
                        @foreach($element->values as $elementValue)
                            <option @if(old('elements.' . $element->short.'.element_value_id', Hoomdossier::getMostCredibleValueFromCollection($buildingElementsOrderedOnCredibility, 'element_value_id')) == $elementValue->id) selected="selected" @endif value="{{ $elementValue->id }}">{{ $elementValue->value }}</option>
                        @endforeach
                    </select>
                @endcomponent
            @endcomponent
        </div>
    @if(!in_array($element->short, $glazingElements))
        </div>
    @elseif(in_array($element->short, $glazingElements))
        @if($glazingCount == 3)
            </div>
        @endif
    @endif
@endforeach