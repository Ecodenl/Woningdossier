@php
    $withLabels ??= false;
    // Any object that has the correct columns
    $defaults ??= new stdClass();

    $supportsLvBag = $cooperation->getCountry()->supportsApi(\App\Enums\ApiImplementation::LV_BAG);
@endphp

<div class="{{ $class ?? 'flex flex-wrap w-full' }}" @if(! empty($attr)) {!! $attr !!} @endif
     @if($supportsLvBag) x-data="checkAddress({'correct_address': '{{ route('api.get-address-data') }}'})" @endif>

    @component('cooperation.frontend.layouts.components.form-group', [
        'withInputSource' => false,
        'label' => $withLabels ? __('auth.register.form.postal-code') : '',
        'class' => 'w-full -mt-5  lg:w-1/2 lg:pr-3',
        'inputName' => 'address.postal_code',
        'id' => 'postcode',
    ])
        <input class="form-input" type="text" name="address[postal_code]" id="postcode"
               value="{{ old('address.postal_code', $defaults->postal_code ?? '') }}"
               placeholder="@lang('auth.register.form.postal-code')" x-bind="postcode">
        <p class="text-blue-800 -mt-2 w-full" x-show="showPostalCodeError" x-cloak>
            @lang('auth.register.form.possible-wrong-postal-code')
        </p>
    @endcomponent
    @component('cooperation.frontend.layouts.components.form-group', [
        'withInputSource' => false,
        'label' => $withLabels ? __('auth.register.form.number') : '',
        'class' => 'w-full -mt-5  lg:w-1/4 lg:px-3',
        'inputName' => 'address.number',
        'id' => 'number',
    ])
        <input class="form-input" type="text" name="address[number]" id="number"
               value="{{ old('address.number', $defaults->number ?? '') }}"
               placeholder="@lang('auth.register.form.number')" x-bind="houseNumber">
    @endcomponent
    @component('cooperation.frontend.layouts.components.form-group', [
        'withInputSource' => false,
        'label' => $withLabels ? __('auth.register.form.house-number-extension') : '',
        'labelAttr' => 'style="display: none;"',
        'class' => 'w-full -mt-5 lg:w-1/4 lg:pl-3',
        'inputName' => 'address.house_number_extension',
        'id' => 'extension',
    ])
        <input class="form-input" type="text" name="address[extension]" @if($supportsLvBag) style="display: none;" @endif
               value="{{ old('address.extension', $defaults->extension ?? '') }}"
               placeholder="@lang('auth.register.form.house-number-extension')"
               x-bind="houseNumberExtensionField">
        @if($supportsLvBag)
            {{-- We are not using a custom select here. Because it defines its own x-data, it makes the x-ref invisible for the parent x-data --}}
            <select class="form-input" name="address[extension]" style="display: none;" id="extension"
                    x-bind="houseNumberExtensionSelect">
                {{-- Values will be bound from JS --}}
                <option value="">
                    @lang('auth.register.form.no-extension')
                </option>
                @if(old('address.extension', $defaults->extension ?? ''))
                    <option value="{{ old('address.extension', $defaults->extension ?? '') }}" selected class="old">
                        {{ old('address.extension', $defaults->extension ?? '') }}
                    </option>
                @endif
                <template x-for="extension in availableExtensions">
                    <option x-bind:value="extension" x-text="extension"></option>
                </template>
            </select>
        @endif
    @endcomponent
    @component('cooperation.frontend.layouts.components.form-group', [
        'withInputSource' => false,
        'label' => $withLabels ? __('auth.register.form.street') : '',
        'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3',
        'inputName' => 'address.street',
        'id' => 'street',
    ])
        <input class="form-input" type="text" name="address[street]" id="street"
               value="{{ old('address.street', $defaults->street ?? '') }}"
               placeholder="@lang('auth.register.form.street')" x-bind="street">
    @endcomponent
    @component('cooperation.frontend.layouts.components.form-group', [
        'withInputSource' => false,
        'label' => $withLabels ? __('auth.register.form.city') : '',
        'class' => 'w-full -mt-5 lg:w-1/2 lg:pl-3',
        'inputName' => 'address.city',
        'id' => 'city',
    ])
        <input class="form-input" type="text" name="address[city]" id="city"
               value="{{ old('address.city', $defaults->city ?? '') }}"
               placeholder="@lang('auth.register.form.city')" x-bind="city">
    @endcomponent
</div>