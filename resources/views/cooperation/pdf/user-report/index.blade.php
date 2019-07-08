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

<body>
    <input type="hidden" value="{{$cooperation->name}}" id="cooperation-name">
    @include('cooperation.pdf.user-report.parts.front-page')
</body>

<script type="text/php">
    if ( isset($pdf) ) {
        $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
        $y = 750;
        $x = 450;
        $pdf->page_text($x, $y, "Cooperatienaam - Pagina {PAGE_NUM}", $font, 6, array(0,0,0));
    }
</script>


@component('cooperation.pdf.components.new-page')
    <div class="container">

    </div>
@endcomponent

</html>
