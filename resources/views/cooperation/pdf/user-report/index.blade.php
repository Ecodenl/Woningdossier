<!DOCTYPE html>
<html lang="{{app()->getLocale()}}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">

        @vite('resources/css/pdf/pdf.css')
        {{--
            PostCSS does the same as webpack (converts bold to 700), and the Tailwind defintion by default is 700, so
            we will keep this here.
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
