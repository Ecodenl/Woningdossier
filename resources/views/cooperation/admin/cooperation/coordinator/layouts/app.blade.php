@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-2">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#sidebar" href="#sidebar">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.label')</a>
                                </h4>
                            </div>
                            <ul id="sidebar" class="list-group panel-collapse open collapse in" aria-expanded="true">
                                <li  class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.coordinator.index'])) active @endif"><a  href="{{route('cooperation.admin.cooperation.coordinator.index')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.home')</a></li>
                                <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.coordinator.assign-roles.index'])) active @endif"><a href="{{route('cooperation.admin.cooperation.coordinator.assign-roles.index')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.assign-roles')</a></li>
                                <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.coordinator.coach.index', 'cooperation.admin.cooperation.coordinator.coach.create'])) active @endif"><a href="{{route('cooperation.admin.cooperation.coordinator.coach.index')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.coach')</a></li>
                                <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.coordinator.coach.create'])) active @endif"><a href="{{route('cooperation.admin.cooperation.coordinator.coach.create')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.add-user')</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#sidebar-messages" href="#sidebar-messages">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.messages')</a>
                                    <span class="glyphicon glyphicon-chevron-down"></span>
                                </h4>
                            </div>
                            <ul id="sidebar-messages" class="list-group panel-collapse collapse" aria-expanded="true">
                                <li  class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.coordinator.messages.index'])) active @endif"><a  href="{{route('cooperation.admin.cooperation.coordinator.messages.index')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.home')</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-10">
                @yield('coordinator_content')
            </div>
        </div>
    </div>
@endsection

@push('css')

@push('footer_scripts')
    <script src="{{ asset('js/datatables.js') }}"></script>
    <script src="{{ asset('js/disable-auto-fill.js') }}"></script>

@push('js')
    <script>
        $('.collapse').on('shown.bs.collapse', function(){
            $(this).parent().find(".glyphicon-chevron-down").removeClass("glyphicon-chevron-down").addClass("glyphicon-chevron-up");
        }).on('hidden.bs.collapse', function(){
            $(this).parent().find(".glyphicon-chevron-up").removeClass("glyphicon-chevron-up").addClass("glyphicon-chevron-down");
        });

    </script>
@endpush


