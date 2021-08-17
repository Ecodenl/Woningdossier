@isset($buildingElement)
    <div id="{{$buildingElement}}-info-alert">
        @component('cooperation.frontend.layouts.parts.alert', [
            'color' => 'blue-800', 'dismissible' => false, 'display' => false
        ])
            @lang('general.need-advice-from-specialist-alert')
        @endcomponent
    </div>
@endisset