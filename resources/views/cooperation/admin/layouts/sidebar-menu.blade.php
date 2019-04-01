@switch(\App\Helpers\HoomdossierSession::currentRole())
    @case('cooperation-admin')
    <div id="sidebar" class="col-md-3">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#sidebar-main" href="#sidebar-main">
                                @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.side-nav.label')
                                <span class="glyphicon {{-- @if(str_replace(['coordinator.', 'cooperation-admin.index', 'assign-role'], '', \Route::currentRouteName()) != \Route::currentRouteName())--}} glyphicon-chevron-up {{--@else glyphicon-chevron-down @endif --}}"></span>
                            </a>

                        </h4>
                    </div>
                    <ul id="sidebar-main"
                        class="sidebar list-group panel-collapse  {{--@if(str_replace(['users.', 'cooperation-admin.index', 'assign-role'], '', \Route::currentRouteName()) != \Route::currentRouteName())--}} open collapse in {{--@else collapse @endif--}}"
                        aria-expanded="true">
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.users.index', 'cooperation.admin.buildings.show'])) active @endif">
                            <a href="{{route('cooperation.admin.cooperation.users.index')}}">
                                @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.side-nav.home')
                            </a>
                        </li>
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.coaches.index', 'cooperation.admin.cooperation.coaches.show'])) active @endif">
                            <a href="{{route('cooperation.admin.cooperation.coaches.index')}}">
                                @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.side-nav.coaches')
                            </a>
                        </li>
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.users.create'])) active @endif">
                            <a href="{{route('cooperation.admin.cooperation.users.create')}}">
                                @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.side-nav.create-user')
                            </a>
                        </li>
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.reports.index'])) active @endif">
                            <a href="{{route('cooperation.admin.cooperation.reports.index')}}">
                                @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.side-nav.reports')
                            </a>
                        </li>
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.questionnaires.index'])) active @endif">
                            <a href="{{route('cooperation.admin.cooperation.questionnaires.index')}}">
                                @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.side-nav.questionnaires')
                            </a>
                        </li>
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.example-buildings.index', 'cooperation.admin.example-buildings.edit', 'cooperation.admin.example-buildings.create'])) active @endif">
                            <a href="{{route('cooperation.admin.example-buildings.index')}}">
                                @lang('woningdossier.cooperation.admin.super-admin.side-nav.example-buildings')
                            </a>
                        </li>
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.cooperation-admin.steps.index'])) active @endif">
                            <a href="{{route('cooperation.admin.cooperation.cooperation-admin.steps.index')}}">
                                @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.side-nav.step')
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @break
    @case('coordinator')
    <div id="sidebar" class="col-md-3">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#sidebar-main" href="#sidebar-main">
                                @lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.label')
                                <span class="glyphicon {{--@if(str_replace(['assign-roles.', '.coach.', 'coordinator.index'], '', \Route::currentRouteName()) != \Route::currentRouteName()) --}}glyphicon-chevron-up {{--@else glyphicon-chevron-down @endif--}}"></span>
                            </a>
                        </h4>
                    </div>
                    <ul id="sidebar-main"
                        class="sidebar list-group panel-collapse {{-- @if(str_replace(['assign-roles.', '.coach.', 'coordinator.index'], '', \Route::currentRouteName()) != \Route::currentRouteName()) --}}open collapse in {{--@else collapse @endif --}}"
                        aria-expanded="true">
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.users.index', 'cooperation.admin.buildings.show'])) active @endif">
                            <a href="{{route('cooperation.admin.cooperation.users.index')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.home')</a>
                        </li>
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.coaches.index', 'cooperation.admin.cooperation.coaches.show'])) active @endif">
                            <a href="{{route('cooperation.admin.cooperation.coaches.index')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.coaches')</a>
                        </li>
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.users.create'])) active @endif">
                            <a href="{{route('cooperation.admin.cooperation.users.create')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.add-user')</a>
                        </li>
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.reports.index'])) active @endif">
                            <a href="{{route('cooperation.admin.cooperation.reports.index')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.reports')</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @break
    @case('super-admin')
    @if(stristr(Route::currentRouteName(), 'cooperation-to-manage'))
        <div class="col-md-3">
            <div class="row">
                @if(isset($breadcrumbs))
                    <div class="col-md-12">
                        <ol class="breadcrumb">
                            @foreach($breadcrumbs as $breadcrumb)
                                <li {{Route::currentRouteName() == $breadcrumb['route'] ? 'class="active"' : ''}}>
                                    @if(Route::currentRouteName() == $breadcrumb['route'])
                                        <a href="{{$breadcrumb['url']}}">{{$breadcrumb['name']}}</a>
                                    @else
                                        {{$breadcrumb['name']}}
                                    @endif

                                </li>
                            @endforeach
                        </ol>
                    </div>
                @endif
            </div>
            <div class="row">
                <div id="sidebar" class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#sidebar-main" href="#sidebar-main">
                                            @lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.side-nav.label', [
                                                'cooperation_name' => $cooperationToManage->name
                                            ])

                                            <span class="glyphicon glyphicon-text glyphicon-chevron-up"></span></a>
                                    </h4>
                                </div>
                                <ul id="sidebar-main" class="sidebar list-group panel-collapse open collapse in collapse "
                                    aria-expanded="true">
                                    <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index'])) active @endif">
                                        <a href="{{route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index', ['cooperation' => $cooperation, 'cooperationToManage' => $cooperationToManage])}}">@lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.side-nav.home')</a>
                                    </li>


                                    <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.super-admin.cooperations.cooperation-to-manage.coordinator.index'])) active @endif">
                                        <a href="{{route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.coordinator.index', ['cooperation' => $cooperation, 'cooperationToManage' => $cooperationToManage])}}">@lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.side-nav.coordinator')</a>
                                    </li>

                                    <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.super-admin.cooperations.cooperation-to-manage.cooperation-admin.index'])) active @endif">
                                        <a href="{{route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.cooperation-admin.index', ['cooperation' => $cooperation, 'cooperationToManage' => $cooperationToManage])}}">@lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.side-nav.cooperation-admin')</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
    <div class="col-md-3">
        <div class="row">
            <div id="sidebar" class="col-md-12">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#sidebar-main"
                                       href="#sidebar-main">@lang('woningdossier.cooperation.admin.super-admin.side-nav.label')
                                        <span class="glyphicon glyphicon-text  @if(str_replace(['.coach.index', '.buildings.'], '', \Route::currentRouteName()) != \Route::currentRouteName()) glyphicon-chevron-up @else glyphicon-chevron-down @endif"></span></a>
                                </h4>
                            </div>
                            <ul id="sidebar-main" class="sidebar list-group panel-collapse open collapse in collapse "
                                aria-expanded="true">
                                <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.super-admin.index'])) active @endif">
                                    <a href="{{route('cooperation.admin.super-admin.index')}}">@lang('woningdossier.cooperation.admin.super-admin.side-nav.home')</a>
                                </li>
                                <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.super-admin.cooperations.index'])) active @endif">
                                    <a href="{{route('cooperation.admin.super-admin.cooperations.index')}}">@lang('woningdossier.cooperation.admin.super-admin.side-nav.cooperations')</a>
                                </li>
                                <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.super-admin.translations.index', 'cooperation.admin.super-admin.translations.edit'])) active @endif">
                                    <a href="{{route('cooperation.admin.super-admin.translations.index')}}">@lang('woningdossier.cooperation.admin.super-admin.side-nav.translations')</a>
                                </li>
                                <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.example-buildings.index', 'cooperation.admin.example-buildings.edit', 'cooperation.admin.example-buildings.create'])) active @endif">
                                    <a href="{{route('cooperation.admin.example-buildings.index')}}">@lang('woningdossier.cooperation.admin.super-admin.side-nav.example-buildings')</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @break
    @case('coach')
    <div id="sidebar" class="col-md-3">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#sidebar-main" href="#sidebar-main">
                                @lang('woningdossier.cooperation.admin.coach.side-nav.label')
                                <span class="glyphicon glyphicon-text  glyphicon-chevron-up ">
                                </span>
                            </a>
                        </h4>
                    </div>
                    <ul id="sidebar-main"
                        class="sidebar list-group panel-collapse open collapse in "
                        aria-expanded="true">
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.coach.buildings.index', 'cooperation.admin.buildings.show'])) active @endif">
                            <a href="{{route('cooperation.admin.coach.buildings.index')}}">@lang('woningdossier.cooperation.admin.coach.side-nav.buildings')</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @break
@endswitch