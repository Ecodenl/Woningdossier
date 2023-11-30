<!DOCTYPE html>
<html lang="{{app()->getLocale()}}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">

        <link rel="stylesheet" type="text/css" href="{{ pdfAsset('css/pdf.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/pdf.css') }}">
        {{--
            Because webpack is a clownfest, font-weight: bold is converted into font-weight: 700 which is NOT
            supported by mPDF. So we just redefine it here because apparently it's too diffult to prevent a CSS conversion
         --}}
        <style>
            h1, h2, h3, h4, h5 {
                font-weight: bold;
            }
        </style>


        {{-- Define footer (an/or header) by name --}}
        <style>
            @page {
                /*header: page-header;*/
                footer: page-footer;
            }
        </style>
    </head>

    <body>
        @include('cooperation.pdf.user-report.parts.pages.front-page')

        @include('cooperation.pdf.user-report.parts.pages.action-plan')

        @include('cooperation.pdf.user-report.parts.pages.info-page')

        @include('cooperation.pdf.user-report.parts.pages.simple-scan-answers')

        @if($scanShort !== \App\Models\Scan::LITE)
            @include('cooperation.pdf.user-report.parts.pages.expert-scan-answers')
            @include('cooperation.pdf.user-report.parts.pages.small-measures')
        @elseif(! empty($coachHelp))
            @include('cooperation.pdf.user-report.parts.pages.coach-help')
        @endif


        {{-- Global footer --}}
        @include('cooperation.pdf.user-report.parts.footer-note')
    </body>
</html>
