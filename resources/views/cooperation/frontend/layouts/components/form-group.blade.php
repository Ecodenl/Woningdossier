<div class="form-group {{ $class ?? '' }}" {!! $attr ?? '' !!}>
    <div class="form-header">
        @if(! empty($route))
            <a href="{{$route}}" class="no-underline">
        @endif
                <label class="form-label {{$labelClass ?? ''}} @if(! ($withInputSource ?? true)) max-w-16/20 @endif @if(! empty($route)) cursor-pointer @endif" for="{{ $id ?? '' }}">
                    {!! $label ?? '' !!}
                </label>
        @if(! empty($route))
            </a>
        @endif
        <div class="form-end" wire:ignore>
            @if(($withInputSource ?? true))
                @include('cooperation.frontend.layouts.parts.source-select')
            @endif
            {{-- No need to show the info icon if no info was given --}}
            @if(! empty($modalBodySlot))
                <div class="form-info" x-data="modal()">
                    <i class="icon-md icon-info-light clickable" x-on:click="toggle()"></i>
                    @component('cooperation.frontend.layouts.components.modal', ['id' => $modalId ?? ''])
                        {{ $modalBodySlot ?? '' }}
                    @endcomponent
                </div>
            @endif
        </div>
    </div>

    <div class="input-group {{ $inputGroupClass ?? '' }} @error(($inputName ?? '')) form-error @enderror">
        {{ $slot }}
        @error(($inputName ?? ''))
        <p class="form-error-label">
            {{ $message }}
        </p>
        @enderror
    </div>

</div>