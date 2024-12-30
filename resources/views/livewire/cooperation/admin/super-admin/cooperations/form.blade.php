{{--
    The slugify will slug the name, it should fill the slug field when empty.
    This will happen, but the user wont see it. The focus blocks it from being overwritten
    Thats when the x-model with entangle comes into play, its dirty and it works.
--}}
<div x-data="{ slugged: @entangle('cooperationToEditFormData.slug') }" class="w-full">
    @component('cooperation.frontend.layouts.components.form-group', [
        'withInputSource' => false,
        'label' => __('cooperation/admin/super-admin/cooperations.form.name.label'),
        'id' => "name",
        'class' => 'w-full -mt-5 required',
        'inputName' => "cooperationToEditFormData.name",
        'inputGroupClass' => 'lg:w-1/2'
    ])
        <input id="name" x-on:blur="$wire.slugify()" wire:model.blur="cooperationToEditFormData.name" required
               type="text" class="form-input" name="name"
               placeholder="@lang('cooperation/admin/super-admin/cooperations.form.name.placeholder')">
    @endcomponent

    @component('cooperation.frontend.layouts.components.form-group', [
        'withInputSource' => false,
        'label' => __('cooperation/admin/super-admin/cooperations.form.slug.label'),
        'id' => "slug",
        'class' => 'w-full required',
        'inputName' => "cooperationToEditFormData.slug",
        'inputGroupClass' => 'lg:w-1/2'
    ])
        <input id="slug" x-model="slugged" wire:model.blur="cooperationToEditFormData.slug" required
               type="text" class="form-input"
               placeholder="@lang('cooperation/admin/super-admin/cooperations.form.slug.placeholder')">
    @endcomponent

    @component('cooperation.frontend.layouts.components.form-group', [
        'withInputSource' => false,
        'label' => __('cooperation/admin/super-admin/cooperations.form.cooperation-email.label'),
        'id' => "email",
        'class' => 'w-full',
        'inputName' => "cooperationToEditFormData.cooperation_email",
        'inputGroupClass' => 'lg:w-1/2'
    ])
        <input id="email" wire:model="cooperationToEditFormData.cooperation_email" type="text" class="form-input"
               placeholder="@lang('cooperation/admin/super-admin/cooperations.form.cooperation-email.placeholder')">
    @endcomponent

    @component('cooperation.frontend.layouts.components.form-group', [
        'withInputSource' => false,
        'label' => __('cooperation/admin/super-admin/cooperations.form.website-url.label'),
        'id' => "website",
        'class' => 'w-full',
        'inputName' => "cooperationToEditFormData.website_url",
        'inputGroupClass' => 'lg:w-1/2'
    ])
        <input id="website" wire:model="cooperationToEditFormData.website_url"
               type="text" class="form-input" name="website_url"
               placeholder="@lang('cooperation/admin/super-admin/cooperations.form.website-url.placeholder')">
    @endcomponent

    @component('cooperation.frontend.layouts.components.form-group', [
        'withInputSource' => false,
        'label' => __('cooperation/admin/super-admin/cooperations.form.econobis-wildcard.label'),
        'id' => "econobis-wildcard",
        'class' => 'w-full',
        'inputName' => "cooperationToEditFormData.econobis_wildcard",
        'inputGroupClass' => 'lg:w-1/2'
    ])
        <input id="econobis-wildcard" wire:model="cooperationToEditFormData.econobis_wildcard"
               type="text" class="form-input"
               placeholder="@lang('cooperation/admin/super-admin/cooperations.form.econobis-wildcard.placeholder')">
    @endcomponent

    @component('cooperation.frontend.layouts.components.form-group', [
        'withInputSource' => false,
        'label' => __('cooperation/admin/super-admin/cooperations.form.econobis-api-key.label'),
        'id' => "econobis-api-key",
        'class' => 'w-full',
        'inputName' => "cooperationToEditFormData.econobis_api_key",
        'inputGroupClass' => 'lg:w-1/2'
    ])
        @php
            $placeholder = __('cooperation/admin/super-admin/cooperations.form.econobis-api-key.label');

            if ($hasApiKey) {
                $placeholder = __('cooperation/admin/super-admin/cooperations.form.econobis-api-key.label-replace');
            }
        @endphp
        <input  id="econobis-api-key" wire:model="cooperationToEditFormData.econobis_api_key"
                type="text" class="form-input" @if($clearApiKey) disabled @endif
                placeholder="{{$placeholder}}">
    @endcomponent

    @if($hasApiKey)
        @component('cooperation.frontend.layouts.components.form-group', [
            'withInputSource' => false,
            'id' => "clear-econobis-api-key",
            'class' => 'w-full',
            'inputName' => "clearApiKey",
        ])
            <div class="checkbox-wrapper mb-0">
                <input type="checkbox" id="clear-econobis-api-key" value="1" wire:model.live="clearApiKey">
                <label for="clear-econobis-api-key">
                    <span class="checkmark"></span>
                    <span>@lang('cooperation/admin/super-admin/cooperations.form.econobis-api-key.clear')</span>
                </label>
            </div>
        @endcomponent
    @endif

    <div class="w-full mt-5">
        <button wire:click="save" class="btn btn-green" type="submit">
            @if($cooperationToEdit->exists)
                @lang('default.buttons.update')
            @else
                @lang('default.buttons.store')
            @endif
        </button>
    </div>
</div>
