<!DOCTYPE html>
<html lang="{{app()->getLocale()}}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">

        <link rel="stylesheet" type="text/css" href="{{ pdfAsset('css/pdf.css') }}">
        <title>Document</title>
    </head>

    {{-- This is the frontpage of the pdf, after this a new page must be started with the component. --}}
    <body>
        @include('cooperation.pdf.user-report.parts.pages.front-page')

        @include('cooperation.pdf.user-report.parts.footer-note')
    </body>

    @include('cooperation.pdf.user-report.parts.pages.action-plan')

    @include('cooperation.pdf.user-report.parts.pages.info-page')

    @include('cooperation.pdf.user-report.parts.pages.simple-scan-answers')

</html>
