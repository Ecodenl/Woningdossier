<div id="front-page">

    <div class="container bg-white" id="user-info" style="height: 220px">
        <h1>{{$user->getFullName()}}</h1>
        <h1>{{$building->street}} {{$building->number}} {{$building->extension}}</h1>
        <h1>{{$building->postal_code}} {{$building->city}}</h1>
        <br>
        <h2>{{$userCooperation->name}}</h2>
        <h2>{{date('d-m-Y')}}</h2>
        @php($coachNames = implode(', ', $connectedCoachNames))
        @if(!empty($coachNames))
            <h2>{{trans_choice('pdf/user-report.front-page.intro.connected-coaches', count($connectedCoachNames)).' '.$coachNames}}</h2>
        @endif
    </div>


    @if($userCooperation->hasMedia(\App\Helpers\MediaHelper::LOGO))
        @php($cooperationLogo = $userCooperation->firstMedia(\App\Helpers\MediaHelper::LOGO))

        <div style="text-align: center;">
            <img style="display: block; margin-top: 250px; width: 250px"
                 src="{{ asset($cooperationLogo->getPath()) }}">
        </div>
    @else
        <div id="img-front-page">
            <img src="{{asset('images/pdf-main-images.jpg')}}">
        </div>
    @endif

    <div class="page-footer bg-white" id="intro">
        <h2 class="text-uppercase">@lang('pdf/user-report.front-page.intro.title')</h2>
        <p>@lang('pdf/user-report.front-page.intro.text')</p>
    </div>

</div>

