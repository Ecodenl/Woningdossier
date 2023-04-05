@switch(\App\Helpers\HoomdossierSession::currentRole())
    @case('cooperation-admin')
        <div id="sidebar" class="col-md-2">
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
                                <a href="{{route('cooperation.admin.users.index')}}">
                                    @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.side-nav.home')
                                </a>
                            </li>
                            <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.coaches.index', 'cooperation.admin.cooperation.coaches.show'])) active @endif">
                                <a href="{{route('cooperation.admin.cooperation.coaches.index')}}">
                                    @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.side-nav.coaches')
                                </a>
                            </li>
                            <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.residents.index'])) active @endif">
                                <a href="{{route('cooperation.admin.cooperation.residents.index')}}">
                                    @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.side-nav.residents')
                                </a>
                            </li>
                            <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.users.create'])) active @endif">
                                <a href="{{route('cooperation.admin.users.create')}}">
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
                            <li class="list-group-item @if(Str::startsWith(Route::currentRouteName(), 'cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications') && optional(Route::current())->parameter('type') === \App\Helpers\Models\CooperationMeasureApplicationHelper::EXTENSIVE_MEASURE) active @endif">
                                <a href="{{route('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.index', ['type' => \App\Helpers\Models\CooperationMeasureApplicationHelper::EXTENSIVE_MEASURE])}}">
                                    @lang('cooperation/admin/shared.sidebar.cooperation-measure-applications.extensive')
                                </a>
                            </li>
                            <li class="list-group-item @if(Str::startsWith(Route::currentRouteName(), 'cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications') && optional(Route::current())->parameter('type') === \App\Helpers\Models\CooperationMeasureApplicationHelper::SMALL_MEASURE) active @endif">
                                <a href="{{route('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.index', ['type' => \App\Helpers\Models\CooperationMeasureApplicationHelper::SMALL_MEASURE])}}">
                                    @lang('cooperation/admin/shared.sidebar.cooperation-measure-applications.small')
                                </a>
                            </li>
                            <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.cooperation-admin.settings.index'])) active @endif">
                                <a href="{{route('cooperation.admin.cooperation.cooperation-admin.settings.index')}}">
                                    @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.side-nav.settings')
                                </a>
                            </li>
                            <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.cooperation-admin.scans.index'])) active @endif">
                                <a href="{{route('cooperation.admin.cooperation.cooperation-admin.scans.index')}}">
                                    @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.side-nav.scans')
                                </a>
                            </li>
                            @include('cooperation.admin.layouts.parts.sidebar.manuals-li')
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @break
    @case('coordinator')
        <div id="sidebar" class="col-md-2">
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
                                <a href="{{route('cooperation.admin.users.index')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.home')</a>
                            </li>
                            <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.coaches.index', 'cooperation.admin.cooperation.coaches.show'])) active @endif">
                                <a href="{{route('cooperation.admin.cooperation.coaches.index')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.coaches')</a>
                            </li>
                            <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.residents.index'])) active @endif">
                                <a href="{{route('cooperation.admin.cooperation.residents.index')}}">
                                    @lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.residents')
                                </a>
                            </li>
                            <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.users.create'])) active @endif">
                                <a href="{{route('cooperation.admin.users.create')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.add-user')</a>
                            </li>
                            <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.reports.index'])) active @endif">
                                <a href="{{route('cooperation.admin.cooperation.reports.index')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.reports')</a>
                            </li>
                            @include('cooperation.admin.layouts.parts.sidebar.manuals-li')
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @break
    @case('super-admin')
        @if(stristr(Route::currentRouteName(), 'cooperation-to-manage'))
            @include('cooperation.admin.layouts.parts.sidebar.cooperation-to-manage')
        @else
            @include('cooperation.admin.layouts.parts.sidebar.super-admin')
        @endif
        @break
    @case('coach')
        <div id="sidebar" class="col-md-2">
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
                            @include('cooperation.admin.layouts.parts.sidebar.manuals-li')
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @break
@endswitch