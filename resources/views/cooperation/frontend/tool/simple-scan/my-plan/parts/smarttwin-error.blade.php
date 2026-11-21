{{-- Why the last hand-off failed; SmartTwinHandoffController redirects back with it. --}}
@php
    $smartTwinError = in_array(request('smarttwin_error'), ['not_configured', 'advice_in_progress', 'unsupported_role', 'failed'], true)
        ? request('smarttwin_error')
        : null;
@endphp
@if($smartTwinError)
    @component('cooperation.layouts.components.alert', ['color' => 'red', 'withBackground' => true])
        {{ __("cooperation/frontend/tool.my-plan.smarttwin.errors.{$smartTwinError}") }}
    @endcomponent
@endif
