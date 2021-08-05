<div x-data="sourceSelect({{$initiallyOpen ?? false}})" x-ref="source-select-wrapper" class="source-select-wrapper">
    <select name="{{$inputName ?? 'input_source'}}" x-ref="source-select"
            @if(($disabled ?? false)) disabled @endif style="display: none;">
        <option value="null">@lang('cooperation/frontend/shared.input-sources.no-answer')</option>
        <option value="resident">@lang('cooperation/frontend/shared.input-sources.resident')</option>
        <option value="coach">@lang('cooperation/frontend/shared.input-sources.coach')</option>
        <option value="example-building">@lang('cooperation/frontend/shared.input-sources.example-building')</option>
    </select>

    <div class="input-group">
        <input class="source-select-input select-none" readonly x-ref="source-select-input" x-model="text"
               x-bind:class="'source-' + value" x-on:click="toggle()" x-on:click.outside="open = false">
        <i x-show="open == false" class="icon-xs icon-arrow-down"></i>
        <i x-cloak x-show="open == true" class="icon-xs icon-arrow-up"></i>
    </div>

    @if(! empty($sourceSlot))
        <ul x-cloak x-ref="source-select-options" class="source-select-dropdown" x-show="open">
            {!! $sourceSlot ?? '' !!}
        </ul>
    @endif
</div>