<!doctype html>
<html lang="{{app()->getLocale()}}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    @if(app()->environment() === 'local')
        @php
            $href = $_SERVER['DOCUMENT_ROOT'] . '/css/pdf.css';
        @endphp
    @else
        @php
            $href = asset('css/pdf.css');
        @endphp
    @endif
    <link rel="stylesheet" type="text/css" href="{{ $href }}">
    <title>Document</title>
</head>

{{-- This is the frontpage of the pdf, after this a new page must be started with the component. --}}
<body>
    <footer>
        @include('cooperation.pdf.user-report.parts.footer-note')
    </footer>

    @include('cooperation.pdf.user-report.parts.front-page')
</body>


{{--    General data is not structured like $reportData--}}
{{--    So have to create our own order.--}}


@include('cooperation.pdf.user-report.steps.general-data-page-1', [
    'stepShort' => 'general-data'
])

@component('cooperation.pdf.components.new-page')
    <div class="container">
        @include('cooperation.pdf.user-report.steps.general-data-page-2')
    </div>
@endcomponent

@component('cooperation.pdf.components.new-page')
    <div class="container">
        @include('cooperation.pdf.user-report.steps.general-data-page-3')
    </div>
@endcomponent
    
@foreach($reportData as $stepShort => $dataForStep)
    @php
        $hasResidentCompletedStep = $building->hasCompleted(
            \App\Models\Step::withGeneralData()->where('short', $stepShort)->first(),
            $inputSource
        );
    @endphp
    @if(array_key_exists($stepShort, $stepShorts) && $hasResidentCompletedStep)
        @foreach ($dataForStep as $subStepShort => $dataForSubStep)
            @php
                $shortToUseAsMainSubject = $subStepShort == '-' ? $stepShort : $subStepShort
            @endphp
            @include('cooperation.pdf.user-report.parts.measure-page')
        @endforeach
    @endif
@endforeach

@foreach($newReportData as $stepShort => $dataForStep)
    @php
        $hasResidentCompletedStep = $building->hasCompleted(
            \App\Models\Step::where('short', $stepShort)->first(),
            $inputSource
        );
        $stepAnswerMap = [
            'heater' => 'sun-boiler',
            'high-efficiency-boiler' => 'hr-boiler',
            'heat-pump' => 'heat-pump',
        ];
    @endphp
    @if(array_key_exists($stepShort, $stepShorts) && $hasResidentCompletedStep)
        @php
            // We don't use this, however to not break code we will set it.
            $subStepShort = $stepShort;
            $shortToUseAsMainSubject = $stepShort;

            $showPage = true;
            if (array_key_exists($stepShort, $stepAnswerMap)) {
                $newHeatSourceQuestion = \App\Models\ToolQuestion::findByShort('new-heat-source');
                $newHeatSourceWaterQuestion = \App\Models\ToolQuestion::findByShort('new-heat-source-warm-tap-water');
                $newSituation = array_merge($building->getAnswer($inputSource, $newHeatSourceQuestion), $building->getAnswer($inputSource, $newHeatSourceWaterQuestion));

                $showPage = in_array($stepAnswerMap[$stepShort], $newSituation);
            }
        @endphp
        @if($showPage)
            @include('cooperation.pdf.user-report.parts.measure-page')
        @endif
    @endif
@endforeach

@include('cooperation.pdf.user-report.parts.outro')

</html>
