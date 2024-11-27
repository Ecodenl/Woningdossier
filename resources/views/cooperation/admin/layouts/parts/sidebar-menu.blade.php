@php
    $currentRole = HoomdossierSession::getRole(true)->name;
    $params = [];
    if(str_contains(Route::currentRouteName(), 'cooperation-to-manage')) {
        $currentRole = 'cooperation-to-manage';
        $params = ['cooperation_name' => $cooperationToManage->name];
    }

    $sidebarTitle = __("woningdossier.cooperation.admin.{$currentRole}.side-nav.label", $params)
@endphp

@component('cooperation.admin.layouts.parts.sidebar.base', [
    'sidebarTitle' => $sidebarTitle
])
    @include("cooperation.admin.layouts.parts.sidebar.{$currentRole}")
@endcomponent