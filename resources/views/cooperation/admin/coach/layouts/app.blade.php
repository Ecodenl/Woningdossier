@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#sidebar" href="#sidebar">@lang('woningdossier.cooperation.admin.coach.side-nav.label')</a>
                        </h4>
                    </div>
                    <ul id="sidebar" class="list-group panel-collapse open collapse in" aria-expanded="true">
                        {{--<li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.coach.index'])) active @endif"><a href="{{route('cooperation.admin.coach.index')}}">@lang('woningdossier.cooperation.admin.coach.side-nav.index')</a></li>--}}
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.coach.buildings.index'])) active @endif"><a href="{{route('cooperation.admin.coach.buildings.index')}}">@lang('woningdossier.cooperation.admin.coach.side-nav.buildings')</a></li>
                        <li class="list-group-item"><a href="#">@lang('woningdossier.cooperation.admin.coach.side-nav.messages')</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-10">
                @yield('coach_content')
            </div>
        </div>
    </div>
@endsection
