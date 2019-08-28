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
@component('cooperation.pdf.components.new-page')
    <div class="container">
        @include('cooperation.pdf.user-report.steps.general-data-page-1')
    </div>
@endcomponent

@component('cooperation.pdf.components.new-page')
    <div class="container">
        @include('cooperation.pdf.user-report.steps.general-data-page-2')
    </div>
@endcomponent


@foreach ($reportData as $step => $data)
    @if (is_string($step) && array_key_exists($step, $stepSlugs))
        @include('cooperation.pdf.user-report.parts.measure-page')
    @endif
@endforeach

</html>
