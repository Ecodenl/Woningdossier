@php
    $stepIconMap = [
        'wall-insulation' => 'icons/wall-insulation-excellent.svg',
        'ventilation' => 'icons/ventilation.svg',
        'insulated-glazing' => 'icons/glass-hr-tp.png',
        'floor-insulation' => 'icons/floor-insulation-excellent.svg',
        'roof-insulation' => 'icons/roof-insulation-excellent.svg',
        'solar-panels' => 'icons/solar-panels.svg',
        'heating' => ''
    ];
@endphp

<div class="step-intro">
    <img src="{{asset($stepIconMap[$stepShort])}}">
    <h2>{{\App\Models\Step::findByShort($stepShort)->name}}</h2>
    <p>@lang('pdf/user-report.step-description.'.$stepShort )</p>
</div>
