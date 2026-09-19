{{--
    The frontend shell: everything above the page content. It sits between the HTML shell
    (cooperation.layouts.app) and the pages, so a page no longer has to know how the header is built
    in order to have one.

    Two modes live here. With SmartTwin disabled the header is the navbar as it has always been. With
    SmartTwin enabled the technical part of the scan happens in SmartTwin, so Hoomdossier shows a
    slimmer shell: a logo bar, a text navigation, and a context bar for the things that used to sit
    between the icons in the navbar.

    Pages fill @section('sub_nav') if they have a second navigation row, and @section('main') as
    before. This layout deliberately does not define 'main': the pages under it differ too much for a
    shared container to be worth it.
--}}
@extends('cooperation.layouts.app')

@section('header')
    <div class="w-full">
        @if(Hoomdossier::hasEnabledSmartTwinCalls())
            @include('cooperation.frontend.layouts.parts.smart-twin.header')
            @include('cooperation.frontend.layouts.parts.smart-twin.nav')
            @include('cooperation.frontend.layouts.parts.smart-twin.context-bar')
        @else
            @include('cooperation.frontend.layouts.parts.navbar')
        @endif

        @yield('sub_nav')
    </div>
@endsection
