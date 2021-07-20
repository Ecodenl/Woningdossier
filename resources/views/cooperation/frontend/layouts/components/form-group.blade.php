<div class="form-group {{ $class ?? '' }} @error(($inputName ?? '')) form-error @enderror">
    <div class="form-header">
        {{-- NOTE: Keep on one line, else white-space: break-spaces will fuck up! This is needed, else
        styling for e.g. inline spans becomes really ugly! --}}
        <label class="form-label" for="{{ $id ?? '' }}">{!! $label ?? '' !!}</label>
        <div class="form-end">
            @include('cooperation.frontend.layouts.parts.source-select')
            {{-- No need to show the info icon if no info was given --}}
            @if(! empty($modalBodySlot))
                <div class="form-info" x-data="modal()">
                    <i class="icon-md icon-info-light" x-on:click="toggle()"></i>
                    @component('cooperation.frontend.layouts.components.modal')
                        {{ $modalBodySlot ?? '' }}
                    @endcomponent
                </div>
            @endif
        </div>
    </div>

    <div class="input-group">
        {{ $slot }}
    </div>

    @error(($inputName ?? ''))
    <p class="form-error-label">
        {{ $message }}
    </p>
    @enderror
</div>