@extends('cooperation.tool.layout')

@section('step_content')

    <form class="form-horizontal" method="POST" id="main-form" action="{{ route('cooperation.tool.general-data.current-state.store') }}" autocomplete="off">
        {{ csrf_field() }}

        <div class="row">
            @foreach($elements as $i => $element)
                @if(!in_array($element->short, ['sleeping-rooms-windows', 'living-rooms-windows']))
                    <div class="row">
                @endif
                        <div class=" col-sm-4 ">
                            @component('cooperation.tool.components.step-question', ['id' => 'element.'.$element->id, 'translation' => 'general-data.element.'.$element->short])
                                @component('cooperation.tool.components.input-group', ['inputType' => 'select', 'inputValues' => $element->values, 'userInputValues' => $building->buildingElements()->forMe()->where('element_id', $element->id)->get(), 'userInputColumn' => 'element_value_id'])
                                    <select id="element_{{ $element->id }}" class="form-control" name="element[{{ $element->id }}]">
                                        @foreach($element->values as $elementValue)
                                            <option @if(old('element.' . $element->id, \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingElements()->where('element_id', $element->id), 'element_value_id')) == $elementValue->id) selected="selected" @endif value="{{ $elementValue->id }}">{{ $elementValue->value }}</option>
                                        @endforeach
                                    </select>
                                @endcomponent
                            @endcomponent
                        </div>
                @if(!in_array($element->short, ['sleeping-rooms-windows', 'living-rooms-windows']))
                    </div>
                @endif
            @endforeach
        </div>

        @include('cooperation.tool.includes.comment', [
            'columnName' => 'step_comments[comment]',
            'translation' => 'general-data/current-state.comment'
        ])
    </form>
@endsection

@push('js')
@endpush