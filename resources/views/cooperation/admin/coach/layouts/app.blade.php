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
                                    <span class="glyphicon glyphicon-text  @if(str_replace(['assign-roles', '.coach.index'], '', \Route::currentRouteName()) != \Route::currentRouteName()) glyphicon-chevron-up @else glyphicon-chevron-down @endif"></span>
                                </h4>
                            </div>
                            <ul id="sidebar-main" class="sidebar list-group panel-collapse @if(str_replace(['.coach.index'], '', \Route::currentRouteName()) != \Route::currentRouteName()) open collapse in @else collapse @endif" aria-expanded="true">
                                <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.coach.index'])) active @endif"><a href="{{route('cooperation.admin.coach.index')}}">@lang('woningdossier.cooperation.admin.coach.side-nav.index')</a></li>
                                <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.coach.buildings.index'])) active @endif"><a href="{{route('cooperation.admin.coach.buildings.index')}}">@lang('woningdossier.cooperation.admin.coach.side-nav.buildings')</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#sidebar-messages" href="#sidebar-messages">@lang('woningdossier.cooperation.admin.coach.side-nav.messages-menu')</a>
                                    <span class="glyphicon glyphicon-text  @if(str_replace(['.messages.', '.connect-to-resident.'], '', \Route::currentRouteName()) != \Route::currentRouteName()) glyphicon-chevron-up @else glyphicon-chevron-down @endif"></span>
                                </h4>
                            </div>
                            <ul id="sidebar-messages" class="sidebar list-group panel-collapse @if(str_replace(['.messages.', '.connect-to-resident.'], '', \Route::currentRouteName()) != \Route::currentRouteName()) open collapse in @else collapse @endif" aria-expanded="true">
                                <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.coach.messages.index', 'cooperation.admin.coach.messages.edit'])) active @endif"><a href="{{route('cooperation.admin.coach.messages.index')}}">@lang('woningdossier.cooperation.admin.coach.side-nav.messages')</a></li>
                                <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.coach.connect-to-resident.index'])) active @endif"><a href="{{route('cooperation.admin.coach.connect-to-resident.index')}}">@lang('woningdossier.cooperation.admin.coach.side-nav.connect-to-resident')</a></li>
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


@prepend('css')
    <link rel="stylesheet" href="{{asset('css/select2/select2.min.css')}}">
    <link rel="stylesheet" rel="stylesheet" type="text/css" href="{{asset('css/datatables/responsive.dataTables.min.css')}}">
    <link rel="stylesheet" rel="stylesheet" type="text/css" href="{{asset('css/datatables/dataTables.bootstrap.min.css')}}">
    <link rel="stylesheet" rel="stylesheet" type="text/css" href="{{asset('css/datatables/responsive.bootstrap.min.css')}}">

@prepend('js')

    <script src="{{ asset('js/datatables.js') }}"></script>
    <script src="{{ asset('js/disable-auto-fill.js') }}"></script>
    <script src="{{asset('js/select2.js')}}"></script>

    <script>
        $(document).ready(function () {

            $('.collapse').on('shown.bs.collapse', function(){
                $(this).parent().find(".glyphicon-chevron-down").removeClass("glyphicon-chevron-down").addClass("glyphicon-chevron-up");
            }).on('hidden.bs.collapse', function(){
                $(this).parent().find(".glyphicon-chevron-up").removeClass("glyphicon-chevron-up").addClass("glyphicon-chevron-down");
            });
        });

        $.extend( true, $.fn.dataTable.defaults, {
            language: {
                url: "{{asset('js/datatables-dutch.json')}}"
            },
        });
    </script>
@endprepend
