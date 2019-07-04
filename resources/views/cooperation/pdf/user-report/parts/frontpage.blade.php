<?php
$user = Auth::user();
$building = $user->buildings->first();
?>

<h1>{{$user->getFullName()}}</h1>
<h1>{{$building->street}} {{$building->number}} {{$building->extension}}</h1>
<h1>{{$building->postal_code}} {{$building->city}}</h1>

<img src="{{asset('images/pdf-main-images.jpg')}}" alt="">

<div class="capitalize">
    <h2>Stappenplan voor het verduurzamen van uw woning</h2>
    <p>U hebt het Hoomdossier voor uw woning ingevuld.</p>
</div>
