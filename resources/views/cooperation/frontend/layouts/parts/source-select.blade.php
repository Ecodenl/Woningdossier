<div x-data="sourceSelect('{{$defaultInputSource ?? 'no-match'}}')" x-ref="source-select-wrapper" class="source-select-wrapper">
    <select x-ref="source-select" x-model="value"
            @if(($disabled ?? false)) disabled @endif style="display: none;">
        <option value="no-match">@lang('cooperation/frontend/shared.input-sources.view-answers')</option>
        <option value="null">@lang('cooperation/frontend/shared.input-sources.no-answer')</option>
        <option value="resident">@lang('cooperation/frontend/shared.input-sources.resident')</option>
        <option value="coach">@lang('cooperation/frontend/shared.input-sources.coach')</option>
        <option value="example-building">@lang('cooperation/frontend/shared.input-sources.example-building')</option>
        <option value="master">@lang('cooperation/frontend/shared.input-sources.master')</option>
    </select>

    <input class="source-select-input select-none" readonly x-ref="source-select-input" x-model="text"
           x-bind:class="'source-' + value" x-on:click="toggle()" x-on:click.outside="open = false">
    <i x-show="open == false && !disabled" class="icon-xs icon-arrow-down"></i>
    <i x-cloak x-show="open == true && !disabled" class="icon-xs icon-arrow-up"></i>

    <ul x-cloak x-ref="source-select-options" class="source-select-dropdown" x-show="open && !disabled">
        @if(! empty($sourceSlot))
            {!! $sourceSlot ?? '' !!}
        @endif
    </ul>
</div>