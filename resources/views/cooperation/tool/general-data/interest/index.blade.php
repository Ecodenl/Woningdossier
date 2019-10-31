@extends('cooperation.tool.layout')

@push('css')
    <link rel="stylesheet" href="{{asset('css/select2/select2.min.css')}}">
@endpush

@section('step_content')

    <form class="form-horizontal" method="POST" id="main-form" action="{{ route('cooperation.tool.general-data.usage.store') }}" autocomplete="off">
        {{ csrf_field() }}
        {{--<div class="col-sm-12 col-md-4 ">--}}
            {{--<img class="img-responsive mt-15 pr-10 d-inline pull-left" src="{{asset('images/element-icons/'.\App\Helpers\StepHelper::ELEMENT_TO_SHORT[$element->short].'.png')}}">--}}
            {{--@component('cooperation.tool.components.step-question', ['id' => 'element.'.$element->id, 'translation' => 'general-data/current-state.element.'.$element->short])--}}
                {{--@component('cooperation.tool.components.input-group', ['inputType' => 'select', 'inputValues' => $element->values, 'userInputValues' => $building->buildingElements()->forMe()->where('element_id', $element->id)->get(), 'userInputColumn' => 'element_value_id'])--}}
                    {{--<select id="element_{{ $element->id }}" class="form-control" name="element[{{ $element->id }}]">--}}
                        {{--@foreach($element->values as $elementValue)--}}
                            {{--<option @if(old('element.' . $element->id, \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingElements()->where('element_id', $element->id), 'element_value_id')) == $elementValue->id) selected="selected" @endif value="{{ $elementValue->id }}">{{ $elementValue->value }}</option>--}}
                        {{--@endforeach--}}
                    {{--</select>--}}
                {{--@endcomponent--}}
            {{--@endcomponent--}}
        {{--</div>--}}

        <div class="row">
            <div class="col-sm-12">
                <h4>@lang('general-data/interest.motivation.title.title')</h4>
                <p>@lang('general-data/interest.motivation.title.help')</p>
            </div>
            <?php
                $oldMotivations = old('motivations');
                $motivationsToSelect = empty(is_array($oldMotivations) ? $oldMotivations : []) ? $userMotivations->pluck('motivation_id')->toArray() : $motivationsToSelect;
            ?>
            <div class="col-sm-12">
                <select id="motivation" class="form-control" name="motivations[]" multiple="multiple">
                    @foreach($motivations as $motivation)
                        <option @if(in_array($motivation->id, $motivationsToSelect)) selected @endif value="{{$motivation->id}}">{{$motivation->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
{{--            @component('cooperation.tool.components.step-question', ['id' => 'element.'.$element->id, 'translation' => 'general-data/current-state.element.'.$element->short])--}}
{{--                @component('cooperation.tool.components.input-group', ['inputType' => 'select', 'inputValues' => $element->values, 'userInputValues' => $building->buildingElements()->forMe()->where('element_id', $element->id)->get(), 'userInputColumn' => 'element_value_id'])--}}
                    {{--<input type="text" name="" class="form-control">--}}
                {{--@endcomponent--}}
            {{--@endcomponent--}}
        </div>
        <br>
        <br>
        <br>
        @include('cooperation.tool.includes.comment', [
            'columnName' => 'step_comments[comment]',
            'translation' => 'general-data/interest.comment'
        ])

    </form>
@endsection

@push('js')
    <!-- select2 -->
    <script src="{{asset('js/select2.js')}}"></script>

    <script>
        $(document).ready(function () {
            $('#motivation').select2();
        });
    </script>
@endpush