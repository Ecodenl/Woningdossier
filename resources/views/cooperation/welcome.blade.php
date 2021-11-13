@extends('cooperation.layouts.app')

@push('css')
    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        .title {
            font-size: 84px;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
@endpush

@section('content')
    <div class="container text-center">

        <h1 class="title m-b-md">
            {{ $cooperation->name }}
        </h1>
        <h2>{{ config('app.name') }}</h2>

        <h2>We werken nog aan de nieuwe versie van het Hoomdossier. Deze komt binnenkort beschikbaar. Je kan tot die tijd gewoon in deze versie werken.</h2>

    </div>
@endsection
