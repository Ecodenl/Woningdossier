@php
    $stepIconMap = [
    'wall-insulation' => 'icons/wall-insulation-excellent.svg',
    'ventilation' => 'icons/ventilation.svg',
    'insulated-glazing' => 'icons/glass-hr-tp.png',
    'floor-insulation' => 'icons/floor-insulation-excellent.svg',
    'roof-insulation' => 'icons/roof-insulation-excellent.svg',
    'high-efficiency-boiler' => 'icons/central-heater.svg',
    'solar-panels' => 'icons/solar-panels.svg',
    'heater' => 'icons/sun-boiler-both.svg'
];

@endphp

<div class="step-intro">
    <img src="{{asset($stepIconMap[$shortToUseAsMainSubject])}}">
    <h2>{{\App\Models\Step::findByShort($shortToUseAsMainSubject)->name}}</h2>
    <p>@lang('pdf/user-report.step-description.'.$shortToUseAsMainSubject )</p>
</div>
