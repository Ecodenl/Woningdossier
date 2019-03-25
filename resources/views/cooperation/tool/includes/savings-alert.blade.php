@isset($buildingElement)
    <div id="{{$buildingElement}}-info-alert">
        @component('cooperation.tool.components.alert', ['type' => 'info', 'hide' => true])
            {{\App\Helpers\Translation::translate('general.need-advice-from-specialist-alert')}}
        @endcomponent
    </div>
@endisset