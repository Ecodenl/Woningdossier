{{--
    The frontend shell: everything above the page content. It sits between the HTML shell
    (cooperation.layouts.app) and the pages, so a page no longer has to know how the header is built
    in order to have one.

    Pages fill @section('sub_nav') if they have a second navigation row, and @section('main') as
    before. This layout deliberately does not define 'main': the pages under it differ too much for a
    shared container to be worth it.
--}}
@extends('cooperation.layouts.app')

@section('header')
    <div class="w-full">
        @include('cooperation.frontend.layouts.parts.navbar')

        @yield('sub_nav')
    </div>
@endsection
