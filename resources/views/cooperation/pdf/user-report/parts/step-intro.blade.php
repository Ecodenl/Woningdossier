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

<div id="step-intro" class="group">
    <div class="icon-container">
        <img src="{{ pdfAsset($stepIconMap[$step->short]) }}" alt="{{ $step->name }}"
             style="max-width: 100%; max-height: 100%;">
    </div>
    <h2 class="text-green-600">
        {{ $step->name }}
    </h2>
    <p>
        {!! nl2br(strip_tags(__("pdf/user-report.step-description.{$step->short}"))) !!}
    </p>
</div>