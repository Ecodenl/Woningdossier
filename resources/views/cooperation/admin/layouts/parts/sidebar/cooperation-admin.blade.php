<li class="@if(in_array(Route::currentRouteName(), ['cooperation.admin.users.index', 'cooperation.admin.buildings.show', 'cooperation.admin.buildings.edit'])) active @endif">
    <a href="{{route('cooperation.admin.users.index')}}">
        @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.side-nav.home')
    </a>
</li>
<li class="@if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.coaches.index', 'cooperation.admin.cooperation.coaches.show'])) active @endif">
    <a href="{{route('cooperation.admin.cooperation.coaches.index')}}">
        @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.side-nav.coaches')
    </a>
</li>
<li class="@if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.residents.index'])) active @endif">
    <a href="{{route('cooperation.admin.cooperation.residents.index')}}">
        @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.side-nav.residents')
    </a>
</li>
<li class="@if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.users.create'])) active @endif">
    <a href="{{route('cooperation.admin.users.create')}}">
        @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.side-nav.create-user')
    </a>
</li>
<li class="@if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.reports.index'])) active @endif">
    <a href="{{route('cooperation.admin.cooperation.reports.index')}}">
        @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.side-nav.reports')
    </a>
</li>
<li class="@if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.questionnaires.index', 'cooperation.admin.cooperation.questionnaires.create', 'cooperation.admin.cooperation.questionnaires.edit'])) active @endif">
    <a href="{{route('cooperation.admin.cooperation.questionnaires.index')}}">
        @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.side-nav.questionnaires')
    </a>
</li>
<li class="@if(in_array(Route::currentRouteName(), ['cooperation.admin.example-buildings.index', 'cooperation.admin.example-buildings.edit', 'cooperation.admin.example-buildings.create'])) active @endif">
    <a href="{{route('cooperation.admin.example-buildings.index')}}">
        @lang('woningdossier.cooperation.admin.super-admin.side-nav.example-buildings')
    </a>
</li>
<li class="@if(Str::startsWith(Route::currentRouteName(), 'cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications') && (\App\Helpers\Models\CooperationMeasureApplicationHelper::EXTENSIVE_MEASURE === Route::current()?->parameter('type') || (empty(Route::current()?->parameter('type')) && Route::current()?->parameter('cooperationMeasureApplication')?->is_extensive_measure))) active @endif">
    <a href="{{route('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.index', ['type' => \App\Helpers\Models\CooperationMeasureApplicationHelper::EXTENSIVE_MEASURE])}}">
        @lang('cooperation/admin/shared.sidebar.cooperation-measure-applications.extensive')
    </a>
</li>
<li class="@if(Str::startsWith(Route::currentRouteName(), 'cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications') && (\App\Helpers\Models\CooperationMeasureApplicationHelper::SMALL_MEASURE === Route::current()?->parameter('type') || (empty(Route::current()?->parameter('type')) && ! Route::current()?->parameter('cooperationMeasureApplication')?->is_extensive_measure))) active @endif">
    <a href="{{route('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.index', ['type' => \App\Helpers\Models\CooperationMeasureApplicationHelper::SMALL_MEASURE])}}">
        @lang('cooperation/admin/shared.sidebar.cooperation-measure-applications.small')
    </a>
</li>
<li class="@if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.cooperation-admin.settings.index'])) active @endif">
    <a href="{{route('cooperation.admin.cooperation.cooperation-admin.settings.index')}}">
        @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.side-nav.settings')
    </a>
</li>
<li class="@if(in_array(Route::currentRouteName(), ['cooperation.admin.cooperation.cooperation-admin.scans.index'])) active @endif">
    <a href="{{route('cooperation.admin.cooperation.cooperation-admin.scans.index')}}">
        @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.side-nav.scans')
    </a>
</li>
@include('cooperation.admin.layouts.parts.sidebar.manuals-li')