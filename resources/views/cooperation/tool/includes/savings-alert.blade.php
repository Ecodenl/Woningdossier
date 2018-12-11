@isset($buildingElement)
    {{--TODO: put in import csv file when all branches are merged. --}}
    <div id="{{$buildingElement}}-info-alert">
        @component('cooperation.tool.components.alert', ['type' => 'info', 'hide' => true])
            Hoeveel u met deze maatregel kunt besparen hangt ervan wat de isolatiewaarde van de huidige isolatielaag is.
            Voor het uitrekenen van de daadwerkelijke besparing bij het na- isoleren van een reeds geïsoleerde gevel/vloer/dak is aanvullend en gespecialiseerd advies nodig.
        @endcomponent
    </div>
@endisset