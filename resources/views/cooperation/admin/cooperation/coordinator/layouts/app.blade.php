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
                                    <a data-toggle="collapse" data-parent="#sidebar-main" href="#sidebar-main">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.label')</a>
                                    <span class="glyphicon  @if(str_replace(['assign-roles.', '.coach.', 'coordinator.index'], '', \Route::currentRouteName()) != \Route::currentRouteName()) glyphicon-chevron-up @else glyphicon-chevron-down @endif"></span>
                                </h4>
                            </div>
                            <ul id="sidebar-main" class="sidebar list-group panel-collapse @if(str_replace(['assign-roles.', '.coach.', 'coordinator.index'], '', \Route::currentRouteName()) != \Route::currentRouteName()) open collapse in @else collapse @endif" aria-expanded="true">
                                <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.coordinator.index'])) active @endif"><a  href="{{route('cooperation.admin.cooperation.coordinator.index')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.home')</a></li>
                                <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.coordinator.assign-roles.index'])) active @endif"><a href="{{route('cooperation.admin.cooperation.coordinator.assign-roles.index')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.assign-roles')</a></li>
                                <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.coordinator.reports.index'])) active @endif"><a href="{{route('cooperation.admin.cooperation.coordinator.reports.index')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.reports')</a></li>
                                <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.coordinator.coach.index'])) active @endif"><a href="{{route('cooperation.admin.cooperation.coordinator.coach.index')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.coach')</a></li>
                                <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.coordinator.coach.create'])) active @endif"><a href="{{route('cooperation.admin.cooperation.coordinator.coach.create')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.add-user')</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#sidebar-messages" href="#sidebar-messages">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.messages')</a>
                                    <span class="glyphicon @if(str_replace(['assign-roles', 'coach', 'messages'], '', \Route::currentRouteName()) != \Route::currentRouteName()) glyphicon-chevron-up @else glyphicon-chevron-down @endif"></span>
                                </h4>
                            </div>
                            <ul id="sidebar-messages" class="sidebar list-group panel-collapse @if(str_replace(['messages', 'conversation-requests', 'connect-to-coach'], '', \Route::currentRouteName()) != \Route::currentRouteName()) collapse in @else collapse @endif" aria-expanded="true">
                                <li  class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.coordinator.conversation-requests.index', 'cooperation.admin.cooperation.coordinator.conversation-requests.show'])) active @endif"><a  href="{{route('cooperation.admin.cooperation.coordinator.conversation-requests.index')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.home')</a></li>
                                <li  class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.coordinator.messages.index', 'cooperation.admin.cooperation.coordinator.messages.edit'])) active @endif"><a  href="{{route('cooperation.admin.cooperation.coordinator.messages.index')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.my-messages')</a></li>
                                <li  class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.coordinator.connect-to-coach.index'])) active @endif"><a  href="{{route('cooperation.admin.cooperation.coordinator.connect-to-coach.index')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.connect-to-coach')</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                @yield('coordinator_content')
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


