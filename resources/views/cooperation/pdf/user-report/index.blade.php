<!doctype html>
<html lang="{{app()->getLocale()}}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="stylesheet" href="{{asset('css/pdf.css')}}">
    <title>Document</title>

</head>

<?php
    $building = $user->building;
?>

{{-- This is the frontpage of the pdf, after this a new page must be started with the component. --}}
<body>
    @include('cooperation.pdf.user-report.parts.footer-note')

    @include('cooperation.pdf.user-report.parts.front-page', ['user' => $user])
</body>

@component('cooperation.pdf.components.new-page')
    <div class="container">
        @include('cooperation.pdf.user-report.steps.general-data')
    </div>
@endcomponent


@foreach($pdfData['user-data'] as $step => $stepData)
    @if(array_key_exists($step, $stepSlugs))
        @component('cooperation.pdf.components.new-page')
            <div class="container">

{{--            <img class="width: 50px; height: 50px;" src="{{public_path('images/'.$step.'.png')}}" alt=""><h2>{{$step}}</h2>--}}
            <div class="step-intro">
{{--                <img src="{{asset('images/'.$step.'.png')}}" alt="">--}}
                <h2>{{\App\Models\Step::whereSlug($step)->first()->name}}</h2>
            </div>

            <div class="question-answer-section">
                <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.filled-in-data')}}</p>
                @foreach($stepData['filled-data'] as $question => $value)
                    <div class="question-answer">
                        <p class="w-300">{{$question}}</p>
                        <p>{{$value}}</p>
                    </div>
                @endforeach
            </div>

            @if(array_key_exists('calculation', $stepData))
                <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.indicative-costs-and-benefits-for-measure')}}</p>
                @foreach($stepData['calculation'] as $calculationResultType => $calculationResults)
                    <div class="question-answer-section">
                        @if($calculationResultType == 'indicative-costs-and-benefits-for-measure')
                            @foreach($calculationResults as $type => $value)
                                <div class="question-answer">
                                    <p class="w-300">{{$type}}</p>
                                    <p>{{$value}}</p>
                                </div>
                            @endforeach
                        @else
                            <div class="measures">
                                <p class="lead w-300">
                                    {{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.measures.title')}}
                                </p>
                                <p class="lead w-150">
                                    {{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.measures.costs')}}
                                </p>
                                <p class="lead w-150">
                                    {{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.measures.year')}}
                                </p>
                            </div>

                            @foreach($calculationResults as $type => $value)
                                <div class="question-answer">
                                    <p class="w-300">{{$type}}</p>
                                    <p class="w-150">{{$value['costs'] ?? 'what'}}</p>
                                    <p class="w-150">{{$value['year'] ?? 'what'}}</p>
                                </div>
                            @endforeach
                        @endif
                    </div>
                @endforeach
            @endif

            <div class="question-answer-section">
                <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.filled-in-data')}}</p>
                @foreach($commentsByStep[$step] as $inputSourceName => $value)
                    <div class="question-answer">
                        <p class="w-300">{{$question}}</p>
                        <p>{{$value}}</p>
                    </div>
                @endforeach
            </div>


            </div>
        @endcomponent
    @endif
@endforeach



</html>
