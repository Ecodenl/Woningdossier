<li class="@if(in_array(Route::currentRouteName(), ['cooperation.admin.super-admin.index'])) active @endif">
    <a href="{{route('cooperation.admin.super-admin.index')}}">
        @lang('woningdossier.cooperation.admin.super-admin.side-nav.home')
    </a>
</li>
<li class="@if(Str::startsWith(Route::currentRouteName(), 'cooperation.admin.super-admin.clients.')) active @endif">
    <a href="{{route('cooperation.admin.super-admin.clients.index', compact('cooperation'))}}">
        @lang('woningdossier.cooperation.admin.super-admin.side-nav.clients')
    </a>
</li>
<li class="@if(in_array(Route::currentRouteName(), ['cooperation.admin.super-admin.cooperations.index'])) active @endif">
    <a href="{{route('cooperation.admin.super-admin.cooperations.index')}}">
        @lang('woningdossier.cooperation.admin.super-admin.side-nav.cooperations')
    </a>
</li>
<li class="@if(in_array(Route::currentRouteName(), ['cooperation.admin.super-admin.users.index'])) active @endif">
    <a href="{{route('cooperation.admin.super-admin.users.index')}}">
        @lang('woningdossier.cooperation.admin.super-admin.side-nav.users')
    </a>
</li>
<li class="@if(in_array(Route::currentRouteName(), ['cooperation.admin.super-admin.questionnaires.index', 'cooperation.admin.super-admin.questionnaires.filter'])) active @endif">
    <a href="{{route('cooperation.admin.super-admin.questionnaires.index')}}">
        @lang('woningdossier.cooperation.admin.super-admin.side-nav.questionnaires')
    </a>
</li>
<li class="@if(in_array(Route::currentRouteName(), ['cooperation.admin.super-admin.translations.index', 'cooperation.admin.super-admin.translations.edit'])) active @endif">
    <a href="{{route('cooperation.admin.super-admin.translations.index')}}">
        @lang('woningdossier.cooperation.admin.super-admin.side-nav.translations')
    </a>
</li>
<li class="@if(in_array(Route::currentRouteName(), ['cooperation.admin.super-admin.tool-questions.index', 'cooperation.admin.super-admin.tool-questions.edit'])) active @endif">
    <a href="{{route('cooperation.admin.super-admin.tool-questions.index')}}">
        @lang('woningdossier.cooperation.admin.super-admin.side-nav.tool-questions')
    </a>
</li>
<li class="@if(in_array(Route::currentRouteName(), ['cooperation.admin.super-admin.tool-calculation-results.index', 'cooperation.admin.super-admin.tool-calculation-results.edit'])) active @endif">
    <a href="{{route('cooperation.admin.super-admin.tool-calculation-results.index')}}">
        @lang('woningdossier.cooperation.admin.super-admin.side-nav.tool-calculation-results')
    </a>
</li>
<li class="@if(Str::startsWith(Route::currentRouteName(), 'cooperation.admin.super-admin.measure-categories')) active @endif">
    <a href="{{route('cooperation.admin.super-admin.measure-categories.index')}}">
        @lang('cooperation/admin/shared.sidebar.measure-categories')
    </a>
</li>
<li class="@if(Str::startsWith(Route::currentRouteName(), 'cooperation.admin.super-admin.measure-applications')) active @endif">
    <a href="{{route('cooperation.admin.super-admin.measure-applications.index')}}">
        @lang('cooperation/admin/shared.sidebar.measure-applications')
    </a>
</li>
<li class="@if(in_array(Route::currentRouteName(), ['cooperation.admin.example-buildings.index', 'cooperation.admin.example-buildings.edit', 'cooperation.admin.example-buildings.create'])) active @endif">
    <a href="{{route('cooperation.admin.example-buildings.index')}}">
        @lang('woningdossier.cooperation.admin.super-admin.side-nav.example-buildings')
    </a>
</li>
<li class="@if(in_array(Route::currentRouteName(), ['cooperation.admin.super-admin.key-figures.index'])) active @endif">
    <a href="{{route('cooperation.admin.super-admin.key-figures.index')}}">
        @lang('woningdossier.cooperation.admin.super-admin.side-nav.key-figures')
    </a>
</li>
<li class="@if(Str::startsWith(Route::currentRouteName(), 'cooperation.admin.super-admin.cooperation-presets')) active @endif">
    <a href="{{route('cooperation.admin.super-admin.cooperation-presets.index')}}">
        @lang('cooperation/admin/shared.sidebar.cooperation-presets')
    </a>
</li>
<li class="@if(Str::startsWith(Route::currentRouteName(), 'cooperation.admin.super-admin.municipalities')) active @endif">
    <a href="{{route('cooperation.admin.super-admin.municipalities.index')}}">
        @lang('cooperation/admin/shared.sidebar.municipalities')
    </a>
</li>