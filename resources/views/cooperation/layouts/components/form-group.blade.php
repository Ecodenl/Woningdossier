<div class="form-group {{ $class ?? '' }} @error(($inputName ?? '')) form-error @enderror">
    <div class="form-header">
        <label class="form-label" for="{{ $id ?? '' }}">
            {{ $label ?? '' }}
        </label>
        <div class="form-end">
            @include('cooperation.layouts.parts.source-select')
            <div class="form-info">
                <i class="icon-md icon-info-light"></i>
            </div>
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