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
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.users.index', 'cooperation.admin.cooperation.users.show'])) active @endif">
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
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.users.index', 'cooperation.admin.cooperation.users.show'])) active @endif">
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
    <div id="sidebar" class="col-md-3">
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
                                <span class="glyphicon glyphicon-text  @if(str_replace(['.coach.index', '.buildings.'], '', \Route::currentRouteName()) != \Route::currentRouteName()) glyphicon-chevron-up @else glyphicon-chevron-down @endif">
                                </span>
                            </a>
                        </h4>
                    </div>
                    <ul id="sidebar-main"
                        class="sidebar list-group panel-collapse @if(str_replace(['.coach.index', '.buildings.', '.messages.', '.connect-to-resident.'], '', \Route::currentRouteName()) != \Route::currentRouteName()) open collapse in @else collapse @endif"
                        aria-expanded="true">
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.coach.buildings.index', 'cooperation.admin.coach.buildings.show'])) active @endif">
                            <a href="{{route('cooperation.admin.coach.buildings.index')}}">@lang('woningdossier.cooperation.admin.coach.side-nav.buildings')</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @break
@endswitch