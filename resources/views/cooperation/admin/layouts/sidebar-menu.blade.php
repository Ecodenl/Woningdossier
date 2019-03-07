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
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.cooperation-admin.index'])) active @endif">
                            <a href="{{route('cooperation.admin.cooperation.cooperation-admin.index', ['role' => \Spatie\Permission\Models\Role::find(\App\Helpers\HoomdossierSession::getRole())->name])}}">
                                @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.side-nav.home')
                            </a>
                        </li>
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.users.index', 'cooperation.admin.cooperation.users.create'])) active @endif">
                            <a href="{{route('cooperation.admin.cooperation.users.index')}}">
                                @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.side-nav.coaches')
                            </a>
                        </li>
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.cooperation-admin.steps.index', 'cooperation.admin.cooperation.users.create'])) active @endif">
                            <a href="{{route('cooperation.admin.cooperation.cooperation-admin.steps.index')}}">
                                @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.side-nav.create-user')
                            </a>
                        </li>
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.cooperation-admin.assign-role.index'])) active @endif">
                            <a href="{{route('cooperation.admin.cooperation.cooperation-admin.assign-roles.index')}}">
                                @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.side-nav.assign-role')
                            </a>
                        </li>
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.cooperation-admin.messages.index', 'cooperation.admin.cooperation.cooperation-admin.messages.public.edit', 'cooperation.admin.cooperation.cooperation-admin.messages.private.edit'])) active @endif">
                            <a href="{{route('cooperation.admin.cooperation.cooperation-admin.messages.index')}}">
                                @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.side-nav.messages')
                            </a>
                        </li>
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.example-buildings.index', 'cooperation.admin.example-buildings.edit', 'cooperation.admin.example-buildings.create'])) active @endif">
                            <a href="{{route('cooperation.admin.example-buildings.index')}}">
                                @lang('woningdossier.cooperation.admin.super-admin.side-nav.example-buildings')
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
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.coordinator.index'])) active @endif">
                            <a href="{{route('cooperation.admin.cooperation.coordinator.index')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.home')</a>
                        </li>
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.coordinator.assign-roles.index'])) active @endif">
                            <a href="{{route('cooperation.admin.cooperation.coordinator.assign-roles.index')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.assign-roles')</a>
                        </li>
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.coordinator.reports.index'])) active @endif">
                            <a href="{{route('cooperation.admin.cooperation.coordinator.reports.index')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.reports')</a>
                        </li>
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.coordinator.questionnaires.index'])) active @endif">
                            <a href="{{route('cooperation.admin.cooperation.coordinator.questionnaires.index')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.questionnaire')</a>
                        </li>
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.coordinator.building-access.index'])) active @endif">
                            <a href="{{route('cooperation.admin.cooperation.coordinator.building-access.index')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.buildings')</a>
                        </li>
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.users.index'])) active @endif">
                            <a href="{{route('cooperation.admin.cooperation.users.index')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.coach')</a>
                        </li>
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.users.create'])) active @endif">
                            <a href="{{route('cooperation.admin.cooperation.users.create')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.add-user')</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#sidebar-messages" href="#sidebar-messages">
                                @lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.messages')
                                <span class="glyphicon {{--@if(str_replace(['assign-roles', 'coach', 'messages'], '', \Route::currentRouteName()) != \Route::currentRouteName())--}} glyphicon-chevron-up {{--@else glyphicon-chevron-down @endif--}}"></span>
                            </a>
                        </h4>
                    </div>
                    <ul id="sidebar-messages"
                        class="sidebar list-group panel-collapse {{--@if(str_replace(['messages', 'conversation-requests', 'connect-to-coach'], '', \Route::currentRouteName()) != \Route::currentRouteName())--}} open collapse in {{--@else collapse @endif--}}"
                        aria-expanded="true">
                        {{--<li  class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.coordinator.conversation-requests.index', 'cooperation.admin.cooperation.coordinator.conversation-requests.show'])) active @endif"><a  href="{{route('cooperation.admin.cooperation.coordinator.conversation-requests.index')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.home')</a></li>--}}
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.coordinator.messages.index', 'cooperation.admin.cooperation.coordinator.messages.public.edit', 'cooperation.admin.cooperation.coordinator.messages.private.edit'])) active @endif">
                            <a href="{{route('cooperation.admin.cooperation.coordinator.messages.index')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.my-messages')</a>
                        </li>
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.coordinator.connect-to-coach.index'])) active @endif">
                            <a href="{{route('cooperation.admin.cooperation.coordinator.connect-to-coach.index')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.connect-to-coach')</a>
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
                            <a data-toggle="collapse" data-parent="#sidebar-main"
                               href="#sidebar-main">@lang('woningdossier.cooperation.admin.coach.side-nav.label') <span
                                        class="glyphicon glyphicon-text  @if(str_replace(['.coach.index', '.buildings.'], '', \Route::currentRouteName()) != \Route::currentRouteName()) glyphicon-chevron-up @else glyphicon-chevron-down @endif"></span></a>
                        </h4>
                    </div>
                    <ul id="sidebar-main"
                        class="sidebar list-group panel-collapse @if(str_replace(['.coach.index', '.buildings.', '.messages.', '.connect-to-resident.'], '', \Route::currentRouteName()) != \Route::currentRouteName()) open collapse in @else collapse @endif"
                        aria-expanded="true">
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.coach.buildings.index'])) active @endif">
                            <a href="{{route('cooperation.admin.coach.buildings.index')}}">@lang('woningdossier.cooperation.admin.coach.side-nav.buildings')</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#sidebar-messages"
                               href="#sidebar-messages">@lang('woningdossier.cooperation.admin.coach.side-nav.messages-menu')
                                <span class="glyphicon glyphicon-text  @if(str_replace(['.messages.', '.connect-to-resident.'], '', \Route::currentRouteName()) != \Route::currentRouteName()) glyphicon-chevron-up @else glyphicon-chevron-down @endif"></span></a>
                        </h4>
                    </div>
                    <ul id="sidebar-messages"
                        class="sidebar list-group panel-collapse @if(str_replace(['.messages.'], '', \Route::currentRouteName()) != \Route::currentRouteName()) open collapse in @else collapse @endif"
                        aria-expanded="true">
                        <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.coach.messages.index', 'cooperation.admin.coach.messages.edit'])) active @endif">
                            <a href="{{route('cooperation.admin.coach.messages.index')}}">@lang('woningdossier.cooperation.admin.coach.side-nav.messages')</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @break
@endswitch