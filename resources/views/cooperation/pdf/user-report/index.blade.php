<!DOCTYPE html>
<html lang="{{app()->getLocale()}}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">

        <link rel="stylesheet" type="text/css" href="{{ pdfAsset('css/pdf.css') }}">

        {{-- Define footer (an/or header) by name --}}
        <style>
            @page {
                footer: page-footer;
            }
        </style>
    </head>

    <body>
        @include('cooperation.pdf.user-report.parts.pages.front-page')

        @include('cooperation.pdf.user-report.parts.pages.action-plan')

        @include('cooperation.pdf.user-report.parts.pages.info-page')

        @include('cooperation.pdf.user-report.parts.pages.simple-scan-answers')



        @include('cooperation.pdf.user-report.parts.footer-note')
    </body>
</html>
