@foreach($services as $i => $service)

    <?php
        $iconName = array_key_exists($service->short, \App\Helpers\StepHelper::SERVICE_TO_SHORT) ? \App\Helpers\StepHelper::SERVICE_TO_SHORT[$service->short] : $service->short;
    ?>
    <div class="col-sm-4 ">
        <img class="img-responsive mt-15 pr-10 d-inline pull-left" src="{{asset('images/service-icons/'.$iconName.'.png')}}">
        @component('cooperation.tool.components.step-question', ['id' => 'service.'.$service->id, 'translation' => 'general-data/current-state.service.'.$service->short])
            @component('cooperation.tool.components.input-group', ['inputType' => 'select', 'inputValues' => $service->values, 'userInputValues' => $building->buildingservices()->forMe()->where('service_id', $service->id)->get(), 'userInputColumn' => 'service_value_id'])
                <select id="service_{{ $service->id }}" class="form-control" name="service[{{ $service->id }}]">
                    @foreach($service->values as $serviceValue)
                        <option @if(old('service.' . $service->id, \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingservices()->where('service_id', $service->id), 'service_value_id')) == $serviceValue->id) selected="selected" @endif value="{{ $serviceValue->id }}">{{ $serviceValue->value }}</option>
                    @endforeach
                </select>
            @endcomponent
        @endcomponent
    </div>

@endforeach