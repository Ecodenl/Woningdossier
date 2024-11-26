<li class="list-group-item @if(in_array(Route::currentRouteName(), ['cooperation.admin.coach.buildings.index', 'cooperation.admin.buildings.show'])) active @endif">
    <a href="{{route('cooperation.admin.coach.buildings.index')}}">@lang('woningdossier.cooperation.admin.coach.side-nav.buildings')</a>
</li>
@include('cooperation.admin.layouts.parts.sidebar.manuals-li')