<!doctype html>
<html lang="{{app()->getLocale()}}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="stylesheet" href="{{asset('css/pdf.css')}}">
    <title>Document</title>
</head>

{{-- This is the frontpage of the pdf, after this a new page must be started with the component. --}}
<body>
    @include('cooperation.pdf.user-report.parts.footer-note')

    @include('cooperation.pdf.user-report.parts.front-page')
</body>

{{--
    General data is not structured like $reportData
    So have to create our own order.
--}}


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


@foreach ($reportData as $stepShort => $dataForStep)
    <?php
        $hasResidentCompletedStep = $building->hasCompleted(
            \App\Models\Step::findByShort($stepShort),
            $inputSource
        );
    ?>
    @if (array_key_exists($stepShort, $stepShorts) && $hasResidentCompletedStep)
        @foreach ($dataForStep as $subStepShort => $dataForSubStep)
            <?php
                $shortToUseAsMainSubject = $subStepShort == '-' ? $stepShort : $subStepShort
            ?>
            @include('cooperation.pdf.user-report.parts.measure-page')
        @endforeach
    @endif
@endforeach

@include('cooperation.pdf.user-report.parts.outro')


</html>
