@extends('cooperation.admin.layouts.app')

@section('content')

    <div class="container">
        <div class="row">
            <div id="sidebar" class="col-md-3">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#sidebar-main" href="#sidebar-main">@lang('woningdossier.cooperation.admin.coach.side-nav.label')</a>
                                    <span class="glyphicon glyphicon-text  @if(str_replace(['assign-roles', 'coach'], '', \Route::currentRouteName()) != \Route::currentRouteName()) glyphicon-chevron-up @else glyphicon-chevron-down @endif"></span>
                                </h4>
                            </div>
                            <ul id="sidebar-main" class="sidebar list-group panel-collapse @if(str_replace(['coach'], '', \Route::currentRouteName()) != \Route::currentRouteName()) open collapse in @else collapse @endif" aria-expanded="true">
                                <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.coach.index'])) active @endif"><a href="{{route('cooperation.admin.coach.index')}}">@lang('woningdossier.cooperation.admin.coach.side-nav.index')</a></li>
                                <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.coach.buildings.index'])) active @endif"><a href="{{route('cooperation.admin.coach.buildings.index')}}">@lang('woningdossier.cooperation.admin.coach.side-nav.buildings')</a></li>
                                <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.coach.messages.index', 'cooperation.admin.coach.messages.edit'])) active @endif"><a href="{{route('cooperation.admin.coach.messages.index')}}">@lang('woningdossier.cooperation.admin.coach.side-nav.messages')</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                @yield('coach_content')
            </div>
        </div>
    </div>
@endsection



@prepend('js')
    <script src="{{ asset('js/datatables.js') }}"></script>
    <script src="{{ asset('js/disable-auto-fill.js') }}"></script>

    <script>
        $('.collapse').on('shown.bs.collapse', function(){
            $(this).parent().find(".glyphicon-chevron-down").removeClass("glyphicon-chevron-down").addClass("glyphicon-chevron-up");
        }).on('hidden.bs.collapse', function(){
            $(this).parent().find(".glyphicon-chevron-up").removeClass("glyphicon-chevron-up").addClass("glyphicon-chevron-down");
        });

    </script>
@endprepend
