<div x-data="sourceSelect({{$initiallyOpen ?? false}})" x-ref="source-select-wrapper" class="source-select-wrapper">
    <select name="{{$inputName ?? 'input_source'}}" x-ref="source-select"
            @if(($disabled ?? false)) disabled @endif style="display: none;">
        <option value="resident">@lang('cooperation/tool/shared.input-sources.resident')</option>
        <option value="coach">@lang('cooperation/tool/shared.input-sources.coach')</option>
        <option value="example-building">@lang('cooperation/tool/shared.input-sources.example-building')</option>
    </select>

    <div class="input-group">
        <input class="source-select-input" readonly x-ref="source-select-input" x-model="text"
               x-bind:class="'source-' + value" x-on:click="toggle()" x-on:click.outside="open = false">
        <i x-show="open == false"
           class="icon-xs icon-arrow-down" x-on:click="toggle()"></i>
        <i x-show="open == true"
           class="icon-xs icon-arrow-up" x-on:click="toggle()"></i>
    </div>

    <div x-ref="source-select-options" class="source-select-dropdown" x-show="open">
        <span class="source-select-option source-resident" data-value="resident" x-on:click="changeOption($el)">
            @lang('cooperation/tool/shared.input-sources.resident')
        </span>
        <span class="source-select-option source-coach" data-value="coach" x-on:click="changeOption($el)">
            @lang('cooperation/tool/shared.input-sources.coach')
        </span>
        <span class="source-select-option source-example-building" data-value="example-building" x-on:click="changeOption($el)">
            @lang('cooperation/tool/shared.input-sources.example-building')
        </span>
    </div>
</div>