<div>
    <form class="w-full flex flex-wrap"
          wire:submit="save()" autocomplete="off">
        @csrf

        @foreach(Hoomdossier::getSupportedLocales() as $locale)
            @component('cooperation.frontend.layouts.components.form-group', [
                'withInputSource' => false,
                'label' => __('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.name.label'),
                'class' => 'w-full -mt-5',
                'id' => "name-{$locale}",
                'inputName' => "content.name", //.{$locale},
            ])
                <div class="input-group-prepend">
                    {{ $locale }}
                </div>
                <input id="{{ "name-{$locale}" }}" class="form-input" type="text" wire:model="content.name.{{$locale}}"
                       placeholder="@lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.name.placeholder')">
            @endcomponent
            @component('cooperation.frontend.layouts.components.form-group', [
                'withInputSource' => false,
                'label' => __('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.info.label'),
                'id' => "info-{$locale}",
                'class' => 'w-full',
                'inputName' => "content.info", //.{$locale},
            ])
                <div class="input-group-prepend">
                    {{ $locale }}
                </div>
                <textarea id="{{ "info-{$locale}" }}" class="form-input" type="text" wire:model="content.info.{{$locale}}"
                          placeholder="@lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.info.placeholder')"
                ></textarea>
            @endcomponent
        @endforeach

        @component('cooperation.frontend.layouts.components.form-group', [
            'withInputSource' => false,
            'label' => __('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.measure-category.label'),
            'id' => 'measure-category',
            'class' => 'w-full lg:w-1/2 lg:pr-3 ' . ($content['is_extensive_measure'] ? 'hidden' : ''),
            'inputName' => "content.relations.mapping.measure_category",
        ])
            @component('cooperation.frontend.layouts.components.alpine-select', ['withSearch' => true])
                <select class="form-input hidden" wire:model="content.relations.mapping.measure_category"
                        id="measure-category">
                    <option value="" disabled selected>
                        @lang('default.form.dropdown.choose')
                    </option>
                    <option value="" class="text-red">
                        @lang('default.form.dropdown.none')
                    </option>
                    @foreach($measures as $measure)
                        <option value="{{ $measure->id }}">
                            {{ $measure->name }}
                        </option>
                    @endforeach
                </select>
            @endcomponent

        @endcomponent

        <div class="w-full"></div>

        @component('cooperation.frontend.layouts.components.form-group', [
            'withInputSource' => false,
            'label' => __('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.costs-from.label'),
            'id' => "costs-from",
            'class' => 'w-full lg:w-1/2 lg:pr-3',
            'inputName' => "content.costs.from",
        ])
            <div class="input-group-prepend">
                <i class="icon-md icon-moneybag"></i>
            </div>
            <input id="costs-from" class="form-input" type="text" wire:model="content.costs.from"
                   placeholder="@lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.costs-from.placeholder')">
        @endcomponent
        @component('cooperation.frontend.layouts.components.form-group', [
            'withInputSource' => false,
            'label' => __('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.costs-to.label'),
            'id' => "costs-to",
            'class' => 'w-full lg:w-1/2 lg:pl-3',
            'inputName' => "content.costs.to",
        ])
            <div class="input-group-prepend">
                <i class="icon-md icon-moneybag"></i>
            </div>
            <input id="costs-to" class="form-input" type="text" wire:model="content.costs.to"
                   placeholder="@lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.costs-to.placeholder')">
        @endcomponent

        @component('cooperation.frontend.layouts.components.form-group', [
            'withInputSource' => false,
            'label' => __('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.savings.label'),
            'id' => "savings-money",
            'class' => 'w-full lg:w-1/2 lg:pr-3',
            'inputName' => "content.savings_money",
        ])
            <div class="input-group-prepend">
                <i class="icon-md icon-moneybag"></i>
            </div>
            <input id="savings-money" class="form-input" type="text" wire:model="content.savings_money"
                   placeholder="@lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.savings.placeholder')">
        @endcomponent

        <div class="w-full"></div>

        <div class="flex w-full flex-wrap">
            @component('cooperation.frontend.layouts.components.form-group', [
                'withInputSource' => false,
                'label' => __('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.icon.label'),
                'id' => 'icon',
                'class' => 'w-full lg:w-1/2 lg:pr-3',
                'inputName' => "content.extra.icon",
            ])
                @component('cooperation.frontend.layouts.components.alpine-select', ['withSearch' => true, 'icon' => 'icon-detached-house'])
                    <select class="form-input hidden" wire:model.live="content.extra.icon"
                            id="icon">
                        @foreach(File::allFiles(public_path('icons')) as $file)
                            @php
                                $iconName = "icon-" . str_replace(".{$file->getExtension()}", '', $file->getBasename());
                            @endphp
                            <option value="{{ $iconName }}" data-icon="{{ $iconName }}">
                                {{ $iconName }}
                            </option>
                        @endforeach
                    </select>
                @endcomponent
            @endcomponent
        </div>

        @component('cooperation.frontend.layouts.components.form-group', [
            'withInputSource' => false,
            'class' => 'w-full lg:w-1/2 lg:pr-3',
            'inputName' => "content.is_extensive_measure",
        ])
            <div class="checkbox-wrapper">
                <input id="is-extensive-measure" wire:model.live="content.is_extensive_measure"
                       type="checkbox" value="1">
                <label for="is-extensive-measure">
                    <span class="checkmark"></span>
                    <span>
                        @lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.is-extensive-measure.label')
                    </span>
                </label>
            </div>
        @endcomponent

        <div class="w-full mt-5">
            <button class="btn btn-green flex justify-center items-center" type="submit">
                @lang('default.buttons.save')
            </button>
        </div>
    </form>
</div>