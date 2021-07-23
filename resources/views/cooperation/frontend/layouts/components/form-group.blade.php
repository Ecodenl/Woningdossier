<div class="form-group {{ $class ?? '' }}" {!! $attr ?? '' !!}>
    <div class="form-header">
        <label class="form-label" for="{{ $id ?? '' }}">
            {!! $label ?? '' !!}
        </label>
        <div class="form-end">
            @if(($withInputSource ?? true))
                @include('cooperation.frontend.layouts.parts.source-select')
            @endif
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

    <div class="input-group @error(($inputName ?? '')) form-error @enderror">
        {{ $slot }}
        @error(($inputName ?? ''))
        <p class="form-error-label">
            {{ $message }}
        </p>
        @enderror
    </div>

</div>