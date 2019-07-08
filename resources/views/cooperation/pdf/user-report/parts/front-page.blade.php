
<?php
    $user     = Auth::user();
    $building = $user->buildings->first();
?>

<div id="front-page">

    <div class="container bg-white" id="user-info">
        <h1>{{$user->getFullName()}}</h1>
        <h1>{{$building->street}} {{$building->number}} {{$building->extension}}</h1>
        <h1>{{$building->postal_code}} {{$building->city}}</h1>
    </div>

    <div id="img-front-page">
        <img src="{{asset('images/pdf-main-images.jpg')}}">
    </div>

    <div class="page-footer bg-white" id="intro">
        <h2 class="text-uppercase">@lang('pdf/user-report.front-page.intro.title')</h2>
        <p>@lang('pdf/user-report.front-page.intro.text')</p>
    </div>

</div>

