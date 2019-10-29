@foreach($services as $i => $service)

    <?php
        $iconName = array_key_exists($service->short, \App\Helpers\StepHelper::SERVICE_TO_SHORT) ? \App\Helpers\StepHelper::SERVICE_TO_SHORT[$service->short] : $service->short;
    ?>

    @if($service->short == 'heat-pump')
        <div class="row">
            <div class="col-sm-4 ">
                <img class="img-responsive mt-15 pr-10 d-inline pull-left" src="{{asset('images/service-icons/'.$iconName.'.png')}}">
                @component('cooperation.tool.components.step-question', ['id' => 'service.'.$service->id, 'translation' => 'general-data/current-state.service.'.$service->short])
                    @component('cooperation.tool.components.input-group', ['inputType' => 'select', 'inputValues' => $service->values, 'userInputValues' => $building->buildingservices()->forMe()->where('service_id', $service->id)->get(), 'userInputColumn' => 'service_value_id'])
                        <select id="service_{{ $service->id }}" class="form-control" name="service[{{ $service->id }}]">
                            {{--5 is the "vraaggestuurd" value, we need this for a checkbox--}}
                            @foreach($service->values as $serviceValue)
                                <option @if(old('service.' . $service->id, \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingservices()->where('service_id', $service->id), 'service_value_id')) == $serviceValue->id) selected="selected" @endif value="{{ $serviceValue->id }}">{{ $serviceValue->value }}</option>
                            @endforeach
                        </select>
                    @endcomponent
                @endcomponent
            </div>
        </div>
    @endif


    @if($service->short == 'house-ventilation')
        <div class="row">
            <div class="col-sm-4 ">
                <img class="img-responsive mt-15 pr-10 d-inline pull-left" src="{{asset('images/service-icons/'.$iconName.'.png')}}">
                @component('cooperation.tool.components.step-question', ['id' => 'service.'.$service->id, 'translation' => 'general-data/current-state.service.'.$service->short])
                    @component('cooperation.tool.components.input-group', ['inputType' => 'select', 'inputValues' => $service->values, 'userInputValues' => $building->buildingservices()->forMe()->where('service_id', $service->id)->get(), 'userInputColumn' => 'service_value_id'])
                        <select id="service_{{ $service->id }}" class="form-control" name="service[{{ $service->id }}]">
                            {{--5 is the "vraaggestuurd" value, we need this for a checkbox--}}
                            @foreach($service->values->where('calculate_value', '!=', 5) as $serviceValue)
                                <option @if(old('service.' . $service->id, \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingservices()->where('service_id', $service->id), 'service_value_id')) == $serviceValue->id) selected="selected" @endif value="{{ $serviceValue->id }}">{{ $serviceValue->value }}</option>
                            @endforeach
                        </select>
                    @endcomponent
                @endcomponent
            </div>
            <div class="col-sm-8">
{{--                @foreach($service->values->where('calculate_value', '!=', 5) as $serviceValue)--}}
{{--                    <input type="checkbox" name="service[{{$service->id}}][{{$service->}}]">--}}
                {{--@endif--}}
            </div>
        </div>
    @endif
    {{--<div class="col-sm-4 ">--}}
        {{--<img class="img-responsive mt-15 pr-10 d-inline pull-left" src="{{asset('images/service-icons/'.$iconName.'.png')}}">--}}
        {{--@component('cooperation.tool.components.step-question', ['id' => 'service.'.$service->id, 'translation' => 'general-data/current-state.service.'.$service->short])--}}
            {{--@component('cooperation.tool.components.input-group', ['inputType' => 'select', 'inputValues' => $service->values, 'userInputValues' => $building->buildingservices()->forMe()->where('service_id', $service->id)->get(), 'userInputColumn' => 'service_value_id'])--}}
                {{--<select id="service_{{ $service->id }}" class="form-control" name="service[{{ $service->id }}]">--}}
                    {{--@foreach($service->values as $serviceValue)--}}
                        {{--<option @if(old('service.' . $service->id, \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingservices()->where('service_id', $service->id), 'service_value_id')) == $serviceValue->id) selected="selected" @endif value="{{ $serviceValue->id }}">{{ $serviceValue->value }}</option>--}}
                    {{--@endforeach--}}
                {{--</select>--}}
            {{--@endcomponent--}}
        {{--@endcomponent--}}
    {{--</div>--}}

@endforeach