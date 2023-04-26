@component('cooperation.frontend.layouts.components.modal', ['header' => __('cooperation/frontend/tool.form.subject')])
    <div class="flex flex-wrap mb-5">
        @component('cooperation.frontend.layouts.components.form-group', [
           'inputName' => "customMeasureApplicationsFormData.{$index}.name",
           'class' => 'w-full -mt-8 mb-4',
           'id' => "custom-measure-application-{$index}-name-wrapper",
           'withInputSource' => false,
        ])
            <input class="form-input"
                   wire:model="customMeasureApplicationsFormData.{{$index}}.name"
                   id="custom-measure-application-{{$index}}-name"
                   placeholder="@lang('cooperation/frontend/shared.modals.add-measure.subject-placeholder')"
                   @if($disabled) disabled="disabled" @endif
            >
        @endcomponent
        <div class="w-full flex items-center">
            <i class="icon-sm icon-info mr-3"></i>
            <h6 class="heading-6">
                @lang('cooperation/frontend/shared.modals.add-measure.info')
            </h6>
        </div>
        @component('cooperation.frontend.layouts.components.form-group', [
           'inputName' => "customMeasureApplicationsFormData.{$index}.info",
           'class' => 'w-full -mt-4 mb-4',
           'id' => "custom-measure-application-{$index}-info-wrapper",
           'withInputSource' => false,
        ])
            <textarea class="form-input"
                      wire:model="customMeasureApplicationsFormData.{{$index}}.info"
                      id="custom-measure-application-{{$index}}-info"
                      placeholder="@lang('cooperation/frontend/shared.modals.add-measure.info-placeholder')"
                      @if($disabled) disabled="disabled" @endif
                                ></textarea>
        @endcomponent
        <div class="w-full flex flex-wrap items-center">
            <i class="icon-sm icon-info mr-3"></i>
            <h6 class="heading-6">
                @lang('cooperation/frontend/shared.modals.add-measure.measure-category')
            </h6>
        </div>
        @component('cooperation.frontend.layouts.components.form-group', [
           'inputName' => "customMeasureApplicationsFormData.{$index}.measure_category",
           'class' => 'w-full -mt-4 mb-4',
           'id' => "custom-measure-application-{$index}-measure-category-wrapper",
           'withInputSource' => false,
        ])
            @component('cooperation.frontend.layouts.components.alpine-select')
                <select class="form-input hidden"
                        wire:model="customMeasureApplicationsFormData.{{$index}}.measure_category"
                        id="custom-measure-application-{{$index}}-measure-category"
                        @if($disabled) disabled="disabled" @endif
                >
                    <option value="">
                        @lang('default.form.dropdown.choose')
                    </option>
                    @foreach($measures as $measure)
                        <option value="{{ $measure->id }}">
                            {{ $measure->name }}
                        </option>
                    @endforeach
                </select>
            @endcomponent
        @endcomponent
        <div class="w-full flex items-center">
            <i class="icon-sm icon-info mr-3"></i>
            <h6 class="heading-6">
                @lang('cooperation/frontend/shared.modals.add-measure.costs')
            </h6>
        </div>
        @component('cooperation.frontend.layouts.components.form-group', [
           'inputName' => "customMeasureApplicationsFormData.{$index}.costs.from",
           'class' => 'w-1/2 pr-1 -mt-4 mb-4',
           'id' => "custom-measure-application-{$index}-costs-from-wrapper",
           'withInputSource' => false,
        ])
            <input class="form-input"
                   wire:model="customMeasureApplicationsFormData.{{$index}}.costs.from"
                   @if($disabled) disabled="disabled" @endif
                   id="custom-measure-application-{{$index}}-costs-from" placeholder="@lang('default.from')">
        @endcomponent
        @component('cooperation.frontend.layouts.components.form-group', [
            'inputName' => "customMeasureApplicationsFormData.{$index}.costs.to",
            'class' => 'w-1/2 pl-1 -mt-4 mb-4',
            'id' => "custom-measure-application-{$index}-costs-to-wrapper",
            'withInputSource' => false,
        ])
            <input class="form-input"
                   wire:model="customMeasureApplicationsFormData.{{$index}}.costs.to"
                   @if($disabled) disabled="disabled" @endif
                   id="custom-measure-application-{{$index}}-costs-to" placeholder="@lang('default.to')">
        @endcomponent
        <div class="w-full flex items-center">
            <i class="icon-sm icon-info mr-3"></i>
            <h6 class="heading-6">
                @lang('cooperation/frontend/shared.modals.add-measure.savings-money')
            </h6>
        </div>
        @component('cooperation.frontend.layouts.components.form-group', [
            'inputName' => "customMeasureApplicationsFormData.{$index}.savings_money",
            'class' => 'w-full -mt-4 mb-4',
            'id' => "custom-measure-application-{$index}-savings-money-wrapper",
            'withInputSource' => false,
        ])
            <input class="form-input"
                   wire:model="customMeasureApplicationsFormData.{{$index}}.savings_money"
                   id="custom-measure-application-{{$index}}-savings-money"
                   @if($disabled) disabled="disabled" @endif
                   placeholder="@lang('cooperation/frontend/shared.modals.add-measure.savings-money')">
        @endcomponent
    </div>
    <div class="w-full border border-gray fixed left-0"></div>
    <div class="flex flex-wrap justify-center mt-14">
        <button wire:loading.attr="disabled" wire:click="save({{$index}})"
                class="btn btn-purple w-full">
            <i class="icon-xs icon-plus-purple mr-3"></i>
            @if($loop->last)
                @lang('cooperation/frontend/shared.modals.add-measure.save')
            @else
                @lang('cooperation/frontend/shared.modals.add-measure.update')
            @endif
        </button>
    </div>
@endcomponent