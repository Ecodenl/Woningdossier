<div class="col-md-2">
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
                        <ul id="sidebar-main"
                            class="sidebar list-group panel-collapse open collapse in collapse "
                            aria-expanded="true">

                            <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index'])) active @endif">
                                <a href="{{route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index', compact('cooperation', 'cooperationToManage'))}}">
                                    @lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.side-nav.home')
                                </a>
                            </li>
                            <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.show', 'cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.index'])) active @endif">
                                <a href="{{route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.index', compact('cooperation', 'cooperationToManage'))}}">
                                    @lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.side-nav.users')
                                </a>
                            </li>
                            <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.create'])) active @endif">
                                <a href="{{route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.create', compact('cooperation', 'cooperationToManage'))}}">
                                    @lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.add-user')
                                </a>
                            </li>
                            <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.super-admin.cooperations.cooperation-to-manage.coordinator.index'])) active @endif">
                                <a href="{{route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.coordinator.index', compact('cooperation', 'cooperationToManage'))}}">
                                    @lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.side-nav.coordinator')
                                </a>
                            </li>
                            <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.super-admin.cooperations.cooperation-to-manage.cooperation-admin.index'])) active @endif">
                                <a href="{{route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.cooperation-admin.index', compact('cooperation', 'cooperationToManage'))}}">
                                    @lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.side-nav.cooperation-admin')
                                </a>
                            </li>
                            <li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.super-admin.cooperations.cooperation-to-manage.settings.index'])) active @endif">
                                <a href="{{route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.settings.index', compact('cooperation', 'cooperationToManage'))}}">
                                    @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.side-nav.settings')
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>