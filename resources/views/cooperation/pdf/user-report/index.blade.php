<!doctype html>
<html lang="{{app()->getLocale()}}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/1.0.4/tailwind.min.css">
    <title>Document</title>

    <style>
{{--        .center {--}}
{{--            margin: auto;--}}
{{--            width: 60%;--}}
{{--        }--}}
{{--        .pdf-container {--}}
{{----}}
{{--        }--}}
{{--        .capitalize {--}}
{{--            .--}}
{{--        }--}}
        .turquoise {
            color: #29A082;
        }
        h1 {
            font-family: Century Gothic,CenturyGothic,AppleGothic,sans-serif;
            font-size: 26px;
            font-weight: bold;
            color: #29A082;
        }
        h2 {
            font-family: Century Gothic,CenturyGothic,AppleGothic,sans-serif;
            font-size: 16px;
            font-weight: bold;
            color: #29A082;
        }

        /* koptekst */
        h4  {
            font-family: Century Gothic,CenturyGothic,AppleGothic,sans-serif;
            font-size: 10px;
            color: #29A082;
        }

        /* tabal kop celachtergrond */
        p:not(.lead) {
            font-family: Century Gothic,CenturyGothic,AppleGothic,sans-serif;
            color: #000000;
            font-size: 10px;
            line-height: 1.15;
        }

        p.lead {
            font-family: Century Gothic,CenturyGothic,AppleGothic,sans-serif;
            font-weight: bold;
            color: #000000;
            font-size: 10px;
        }
    </style>
</head>
<body>

<div class="container mx-auto">

    @include('cooperation.pdf.user-report.parts.frontpage')

    <p class="lead">inleidings tekst</p>
    <p>stukje tekst</p>
    <br>
    <h2>Koptekst groot</h2>
    <br>
    <h4>koptekst klein</h4>
</div>
</body>
</html>