{{--
    The slugify will slug the name, it should fill the slug field when empty.
    This will happen, but the user wont see it. The focus blocks it from being overwritten
    Thats when the x-model with entangle comes into play, its dirty and it works.
--}}
<div x-data="{ slugged: @entangle('cooperationToEditFormData.slug').defer }">

    <div class="form-group {{ $errors->has('cooperationToEditFormData.name') ? ' has-error' : '' }}">
        <label for="">@lang('woningdossier.cooperation.admin.super-admin.cooperations.create.form.name')</label>
        <input x-on:blur="$wire.slugify()" wire:model.lazy="cooperationToEditFormData.name"  required type="text" class="form-control" name="name"
               placeholder="@lang('woningdossier.cooperation.admin.super-admin.cooperations.create.form.name')">

        @if ($errors->has('cooperationToEditFormData.name'))
            <span class="help-block">
                                    <strong>{{ $errors->first('cooperationToEditFormData.name') }}</strong>
                                </span>
        @endif
    </div>

    <div class="form-group {{ $errors->has('cooperationToEditFormData.slug') ? ' has-error' : '' }}">
        <label for="">@lang('woningdossier.cooperation.admin.super-admin.cooperations.create.form.slug')</label>
        <input x-model="slugged" wire:model.lazy="cooperationToEditFormData.slug"  required type="text" class="form-control" name="slug"
               placeholder="@lang('woningdossier.cooperation.admin.super-admin.cooperations.create.form.slug')">

        @if ($errors->has('cooperationToEditFormData.slug'))
            <span class="help-block">
                                    <strong>{{ $errors->first('cooperationToEditFormData.slug') }}</strong>
                                </span>
        @endif
    </div>

    <div class="form-group {{ $errors->has('cooperationToEditFormData.cooperation_email') ? ' has-error' : '' }}">
        <label for="">@lang('woningdossier.cooperation.admin.super-admin.cooperations.edit.form.cooperation_email')</label>
        <input wire:model="cooperationToEditFormData.cooperation_email"  type="text" class="form-control" name="cooperation_email"
               placeholder="@lang('woningdossier.cooperation.admin.super-admin.cooperations.create.form.cooperation_email')">

        @if ($errors->has('cooperationToEditFormData.cooperation_email'))
            <span class="help-block">
                                    <strong>{{ $errors->first('cooperationToEditFormData.cooperation_email') }}</strong>
                                </span>
        @endif
    </div>

    <div class="form-group {{ $errors->has('cooperationToEditFormData.website_url') ? ' has-error' : '' }}">
        <label for="">@lang('woningdossier.cooperation.admin.super-admin.cooperations.edit.form.website_url')</label>
        <input wire:model="cooperationToEditFormData.website_url"  type="text" class="form-control" name="website_url"
               placeholder="@lang('woningdossier.cooperation.admin.super-admin.cooperations.create.form.website_url')">

        @if ($errors->has('cooperationToEditFormData.website_url'))
            <span class="help-block">
                                    <strong>{{ $errors->first('cooperationToEditFormData.website_url') }}</strong>
                                </span>
        @endif
    </div>


    <button wire:click="save" class="btn btn-success"
            type="submit">@lang('woningdossier.cooperation.admin.super-admin.cooperations.create.form.create')</button>
</div>
