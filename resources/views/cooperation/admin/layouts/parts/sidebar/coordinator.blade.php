<li class="@if(in_array(Route::currentRouteName(), ['cooperation.admin.users.index', 'cooperation.admin.buildings.show'])) active @endif">
    <a href="{{route('cooperation.admin.users.index')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.home')</a>
</li>
<li class="@if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.coaches.index', 'cooperation.admin.cooperation.coaches.show'])) active @endif">
    <a href="{{route('cooperation.admin.cooperation.coaches.index')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.coaches')</a>
</li>
<li class="@if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.residents.index'])) active @endif">
    <a href="{{route('cooperation.admin.cooperation.residents.index')}}">
        @lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.residents')
    </a>
</li>
<li class="@if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.users.create'])) active @endif">
    <a href="{{route('cooperation.admin.users.create')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.add-user')</a>
</li>
<li class="@if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.reports.index'])) active @endif">
    <a href="{{route('cooperation.admin.cooperation.reports.index')}}">@lang('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.reports')</a>
</li>
@include('cooperation.admin.layouts.parts.sidebar.manuals-li')