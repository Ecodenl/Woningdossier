@isset($buildingElement)
    <div id="{{$buildingElement}}-info-alert">
        @component('cooperation.layouts.components.alert', [
            'color' => 'blue-900', 'dismissible' => false, 'display' => false
        ])
            @lang('general.need-advice-from-specialist-alert')
        @endcomponent
    </div>
@endisset