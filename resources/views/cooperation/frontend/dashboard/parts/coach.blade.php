{{--
    Who the energy coach is and when the conversation is. On the dashboard and beside the messages,
    so it lives here once.

    Takes 'coach' (a User or null) and 'appointmentDate' (a Carbon or null); both rows render either
    way, so the tile keeps its shape before a coach is attached. 'withContact' adds the button to the
    messages page — pointless on the messages page itself, so it defaults to on and that page turns
    it off.
--}}
@php
    $withContact = $withContact ?? true;
@endphp

<section class="tile">
    <h3 class="tile-heading">
        @lang('home.dashboard.coach.title')
    </h3>

    @if($coach instanceof \App\Models\User)
        <dl class="coach-fact">
            <dt>@lang('home.dashboard.coach.name')</dt>
            <dd>{{ $coach->getFullName() }}</dd>
        </dl>
    @else
        <p>@lang('home.dashboard.coach.none')</p>
    @endif

    @if(! is_null($appointmentDate))
        <dl class="coach-fact">
            <dt>@lang('home.dashboard.coach.appointment')</dt>
            <dd>{{ $appointmentDate->translatedFormat('j F Y \o\m H.i \u\u\r') }}</dd>
        </dl>
    @else
        <p>@lang('home.dashboard.coach.no-appointment')</p>
    @endif

    @if($withContact)
        <div class="tile-action">
            <a class="btn btn-blue" href="{{ route('cooperation.my-account.messages.edit') }}">
                @lang('home.dashboard.coach.contact')
            </a>
        </div>
    @endif
</section>
