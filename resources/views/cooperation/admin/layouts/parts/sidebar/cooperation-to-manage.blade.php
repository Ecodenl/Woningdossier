@slot('breadcrumbsSlot')
    @if(! empty($breadcrumbs))
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
@endslot

<li class="@if(in_array(Route::currentRouteName(), ['cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index'])) active @endif">
    <a href="{{route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index', compact('cooperation', 'cooperationToManage'))}}">
        @lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.side-nav.home')
    </a>
</li>
<li class="@if(in_array(Route::currentRouteName(), ['cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.show', 'cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.index'])) active @endif">
    <a href="{{route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.index', compact('cooperation', 'cooperationToManage'))}}">
        @lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.side-nav.users')
    </a>
</li>
<li class="@if(in_array(Route::currentRouteName(), ['cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.create'])) active @endif">
    <a href="{{route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.create', compact('cooperation', 'cooperationToManage'))}}">
        @lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.add-user')
    </a>
</li>
<li class="@if(in_array(Route::currentRouteName(), ['cooperation.admin.super-admin.cooperations.cooperation-to-manage.coordinator.index'])) active @endif">
    <a href="{{route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.coordinator.index', compact('cooperation', 'cooperationToManage'))}}">
        @lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.side-nav.coordinator')
    </a>
</li>
<li class="@if(in_array(Route::currentRouteName(), ['cooperation.admin.super-admin.cooperations.cooperation-to-manage.cooperation-admin.index'])) active @endif">
    <a href="{{route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.cooperation-admin.index', compact('cooperation', 'cooperationToManage'))}}">
        @lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.side-nav.cooperation-admin')
    </a>
</li>
<li class="@if(in_array(Route::currentRouteName(), ['cooperation.admin.super-admin.cooperations.cooperation-to-manage.settings.index'])) active @endif">
    <a href="{{route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.settings.index', compact('cooperation', 'cooperationToManage'))}}">
        @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.side-nav.settings')
    </a>
</li>