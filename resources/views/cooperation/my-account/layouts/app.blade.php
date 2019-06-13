@extends('cooperation.layouts.app')

@section('content')

    <div class="container">
        <div class="row">
            <div id="sidebar" class="col-md-3">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#sidebar-main" href="#sidebar-main">@lang('my-account.side-nav.label')</a>
                                    <span class="glyphicon glyphicon-text @if(str_replace(['assign-roles', '.coach.index'], '', \Route::currentRouteName()) != \Route::currentRouteName()) glyphicon-chevron-up @else glyphicon-chevron-down @endif"></span>
                                </h4>
                            </div>
                            <ul id="sidebar-main" class="sidebar list-group panel-collapse @if(str_replace(['my-account'], '', \Route::currentRouteName()) != \Route::currentRouteName()) open collapse in @else collapse @endif" aria-expanded="true">
                                <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.my-account.index'])) active @endif">
                                    <a href="{{route('cooperation.my-account.index')}}">
                                        @lang('my-account.side-nav.home')
                                    </a>
                                </li>
                                <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.my-account.settings.index'])) active @endif">
                                    <a href="{{route('cooperation.my-account.settings.index')}}">
                                        @lang('my-account.side-nav.settings')
                                        <span class="glyphicon glyphicon-cog"></span>
                                    </a>
                                </li>
                                {{--
                                <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.my-account.notification-settings.index', 'cooperation.my-account.notification-settings.show'])) active @endif">
                                    <a href="{{route('cooperation.my-account.notification-settings.index')}}">
                                        @lang('my-account.side-nav.notification-settings')
                                    </a>
                                </li>
                                --}}
                                <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.my-account.import-center.index', 'cooperation.my-account.import-center.edit'])) active @endif">
                                    <a href="{{route('cooperation.my-account.import-center.index')}}">
                                        @lang('my-account.side-nav.import')
                                    </a>
                                </li>
                                <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.my-account.messages.index', 'cooperation.my-account.messages.edit'])) active @endif">
                                    <a href="{{route('cooperation.my-account.messages.index')}}">
                                        @lang('my-account.side-nav.my-messages')
                                    </a>
                                </li>
                                <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.my-account.access.index'])) active @endif">
                                    <a href="{{route('cooperation.my-account.access.index')}}">
                                        @lang('my-account.side-nav.access')
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                @yield('my_account_content')
            </div>
        </div>
    </div>
@endsection


@prepend('css')
    <link rel="stylesheet" href="{{asset('css/select2/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/datatables/responsive.dataTables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/datatables/dataTables.bootstrap.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/datatables/responsive.bootstrap.min.css')}}">
@endprepend
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
