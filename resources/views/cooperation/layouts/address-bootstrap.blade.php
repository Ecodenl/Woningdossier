@php
    $withLabels ??= false;
    // Any object that has the correct columns
    $defaults ??= new stdClass();
@endphp

<div class="{{ $class ?? '' }}" @if(! empty($attr)) {!! $attr !!} @endif
     x-data="checkAddress({'correct_address': '{{ route('api.get-address-data') }}'})">

    <div class="row">
        <div class="col-xs-12 col-lg-6">
            @component('layouts.parts.components.form-group', [
                'input_name' => 'address.postal_code',
            ])
                @if($withLabels)
                    <label for="postcode" class="control-label">
                        @lang('auth.register.form.postal-code')
                    </label>
                @endif
                <input class="form-control" type="text" name="address[postal_code]" id="postcode"
                       value="{{ old('postal_code', $defaults->postal_code ?? '') }}"
                       placeholder="@lang('auth.register.form.postal-code')" x-bind="postcode">
                <p class="text-blue-800 -mt-2 w-full" x-show="showPostalCodeError">
                    @lang('auth.register.form.possible-wrong-postal-code')
                </p>
            @endcomponent
        </div>
        <div class="col-xs-12 col-lg-3">
            @component('layouts.parts.components.form-group', [
                    'input_name' => 'address.number',
                ])
                @if($withLabels)
                    <label for="number" class="control-label">
                        @lang('auth.register.form.number')
                    </label>
                @endif
                <input class="form-control" type="text" name="address[number]" id="number"
                       value="{{ old('number', $defaults->number ?? '') }}"
                       placeholder="@lang('auth.register.form.number')" x-bind="houseNumber">
            @endcomponent
        </div>
        <div class="col-xs-12 col-lg-3">
            @component('layouts.parts.components.form-group', [
                   'input_name' => 'address.house_number_extension',
               ])
                @if($withLabels)
                    <label for="extension" class="control-label">
                        @lang('auth.register.form.house-number-extension')
                    </label>
                @endif
                <input class="form-control" type="text" name="address[extension]"
                       value="{{ old('extension', $defaults->extension ?? '') }}"
                       placeholder="@lang('auth.register.form.house-number-extension')"
                       x-bind="houseNumberExtensionField">
                {{-- We are not using a custom select here. Because it defines its own x-data, it makes the x-ref invisible for the parent x-data --}}
                <select class="form-control" name="address[extension]" x-cloak id="extension"
                        x-bind="houseNumberExtensionSelect">
                    {{-- Values will be bound from JS --}}
                    <option value="">
                        @lang('auth.register.form.no-extension')
                    </option>
                    @if(old('extension', $defaults->extension ?? ''))
                        <option value="{{ old('extension', $defaults->extension ?? '') }}" selected class="old">
                            {{ old('extension', $defaults->extension ?? '') }}
                        </option>
                    @endif
                    <template x-for="extension in availableExtensions">
                        <option x-bind:value="extension" x-text="extension"></option>
                    </template>
                </select>
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-lg-6">
            @component('layouts.parts.components.form-group', [
                'input_name' => 'address.street',
            ])
                @if($withLabels)
                    <label for="street" class="control-label">
                        @lang('auth.register.form.street')
                    </label>
                @endif
                <input class="form-control" type="text" name="address[street]" id="street"
                       value="{{ old('street', $defaults->street ?? '') }}"
                       placeholder="@lang('auth.register.form.street')" x-bind="street">
            @endcomponent
        </div>
        <div class="col-xs-12 col-lg-6">
            @component('layouts.parts.components.form-group', [
                'input_name' => 'address.city',
            ])
                @if($withLabels)
                    <label for="city" class="control-label">
                        @lang('auth.register.form.city')
                    </label>
                @endif
                <input class="form-control" type="text" name="address[city]" id="city"
                       value="{{ old('city', $defaults->city ?? '') }}"
                       placeholder="@lang('auth.register.form.city')" x-bind="city">
            @endcomponent
        </div>
    </div>
</div>