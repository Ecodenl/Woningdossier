@php
    $stepIconMap = [
        'wall-insulation' => 'icons/wall-insulation-excellent.svg',
        'ventilation' => 'icons/ventilation.svg',
        'insulated-glazing' => 'icons/glass-hr-tp.png',
        'floor-insulation' => 'icons/floor-insulation-excellent.svg',
        'roof-insulation' => 'icons/roof-insulation-excellent.svg',
        'high-efficiency-boiler' => 'icons/central-heater.svg',
        'solar-panels' => 'icons/solar-panels.svg',
        'heater' => 'icons/sun-boiler-both.svg',
        'heat-pump' => 'icons/heat-pump.svg',
        'heating' => 'icons/placeholder.svg',
    ];
@endphp

<div class="step-intro">
    <img src="{{asset($stepIconMap[$shortToUseAsMainSubject])}}">
    <h2>{{\App\Models\Step::findByShort($shortToUseAsMainSubject)->name}}</h2>
    <p>{!! nl2br(strip_tags(__("pdf/user-report.step-description.{$shortToUseAsMainSubject}"))) !!}</p>
</div>
