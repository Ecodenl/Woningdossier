<div class="{{ $class ?? 'flex flex-wrap w-full' }}" @if(! empty($attr)) {!! $attr !!} @endif
     x-data="checkAddress({'correct_address': '{{ route('api.get-address-data') }}'})">
    <input type="hidden" name="addressid" x-bind="addressId" value="{{ old('addressid') }}">

    @component('cooperation.frontend.layouts.components.form-group', [
        'withInputSource' => false,
        'class' => 'w-full -mt-5  lg:w-1/2 lg:pr-3',
        'inputName' => 'postal_code',
    ])
        <input class="form-input" type="text" name="postal_code" value="{{ old('postal_code') }}"
               placeholder="@lang('auth.register.form.postal-code')" x-bind="postcode">
        <p class="text-blue-800 -mt-2 w-full" x-show="showPostalCodeError">
            @lang('auth.register.form.possible-wrong-postal-code')
        </p>
    @endcomponent
    @component('cooperation.frontend.layouts.components.form-group', [
        'withInputSource' => false,
        'class' => 'w-full -mt-5  lg:w-1/4 lg:px-3',
        'inputName' => 'number',
    ])
        <input class="form-input" type="text" name="number" value="{{ old('number') }}"
               placeholder="@lang('auth.register.form.number')" x-bind="houseNumber">
    @endcomponent
    @component('cooperation.frontend.layouts.components.form-group', [
        'withInputSource' => false,
        'class' => 'w-full -mt-5 lg:w-1/4 lg:pl-3',
        'inputName' => 'house_number_extension',
    ])
        <input class="form-input" type="text" name="extension"
               value="{{ old('extension') }}"
               placeholder="@lang('auth.register.form.house-number-extension')"
               x-bind="houseNumberExtension">
        {{-- We are not using a custom select here. Because it defines its own x-data, it makes the x-ref invisible for the parent x-data --}}
        <select class="form-input" name="extension" x-cloak
                x-bind="houseNumberExtension">
            {{-- Values will be bound from JS --}}
            <option value="null">
                @lang('auth.register.form.no-extension')
            </option>
            @if(old('extension'))
                <option value="{{ old('extension') }}" selected class="old">
                    {{ old('extension') }}
                </option>
            @endif
            <template x-for="extension in availableExtensions">
                <option x-bind:value="extension" x-text="extension"></option>
            </template>
        </select>
    @endcomponent
    @component('cooperation.frontend.layouts.components.form-group', [
        'withInputSource' => false,
        'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3',
        'inputName' => 'street',
    ])
        <input class="form-input" type="text" name="street" value="{{ old('street') }}"
               placeholder="@lang('auth.register.form.street')" x-bind="street">
    @endcomponent
    @component('cooperation.frontend.layouts.components.form-group', [
        'withInputSource' => false,
        'class' => 'w-full -mt-5 lg:w-1/2 lg:pl-3',
        'inputName' => 'city',
    ])
        <input class="form-input" type="text" name="city" value="{{ old('city') }}"
               placeholder="@lang('auth.register.form.city')" x-bind="city">
    @endcomponent
</div>