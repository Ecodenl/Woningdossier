<!doctype html>
<html lang="{{app()->getLocale()}}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
{{--    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/1.0.4/tailwind.min.css">--}}

    <link rel="stylesheet" href="{{asset('css/pdf.css')}}">
    <title>Document</title>

</head>

<?php
$building = $user->buildings->first();
?>

{{-- This is the frontpage of the pdf, after this a new page must be started with the component. --}}
<body>
    {{--
        Necessary evil for the page count.
        Should be placed in a 'page' otherwise a new page is created
    --}}
    <script type="text/php">
        if ( isset($pdf) ) {
            $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
            $y = 750;
            $x = 450;
            $pdf->page_text($x, $y, "{$GLOBALS['_cooperation']->name} - Pagina {PAGE_NUM}", $font, 6, array(0,0,0));
        }
    </script>

    @include('cooperation.pdf.user-report.parts.front-page', ['user' => $user])
</body>

@component('cooperation.pdf.components.new-page')
    <div class="container">
        @include('cooperation.pdf.user-report.steps.general-data')
    </div>
@endcomponent





</html>
