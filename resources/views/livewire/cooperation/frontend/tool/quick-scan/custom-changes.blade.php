<div class="w-full grid grid-rows-2 grid-cols-4 grid-flow-row justify-items-center">
    {{-- TODO: Make this cooperation measure applications --}}
    <div class="checkbox-wrapper media-wrapper">
        <input type="checkbox" id="changes-kitchen" name="changes" value="kitchen">
        <label for="changes-kitchen">
            <span class="media-icon-wrapper">
                <i class="icon-kitchen"></i>
            </span>
            <span class="checkmark"></span>
            <span>Keuken</span>
        </label>
    </div>
    @foreach($customMeasureApplicationsFormData as $index => $customMeasureApplicationFormData)
        <div class="@if($loop->last) add-option-wrapper @else checkbox-wrapper @endif media-wrapper" x-data="modal()">
            @if(!$loop->last)
                <input type="checkbox" id="custom-measure-{{$index}}" value="attic-room">
            @endif
            <label for="custom-measure-{{$index}}" x-on:click="toggle()">
                <span class="media-icon-wrapper">
                    <i class="@if($loop->last) icon-plus-circle @else{{$customMeasureApplicationFormData['extra']['icon']}} @endif"></i>
                </span>
                @if(!$loop->last)
                    <span class="checkmark" x-on:click.stop></span>
                @else
                    <span>@lang('cooperation/frontend/tool.form.add-option')</span>
                @endif

                <span>{{$customMeasureApplicationFormData['name']}}</span>
            </label>
            @component('cooperation.frontend.layouts.components.modal', ['header' => __('cooperation/frontend/tool.form.subject')])
                <div class="flex flex-wrap mb-5">
                    @component('cooperation.frontend.layouts.components.form-group', [
                       'inputName' => "customMeasureApplicationsFormData.{$index}.subject",
                       'class' => 'w-full -mt-4 mb-4',
                       'id' => 'new-measure-subject',
                       'withInputSource' => false,
                   ])
                        <input class="form-input" wire:model="customMeasureApplicationsFormData.{{$index}}.name"
                               id="new-measure-subject"

                               placeholder="@lang('cooperation/frontend/shared.modals.add-measure.placeholder')">
                    @endcomponent
                    <div class="w-full flex items-center">
                        <i class="icon-sm icon-info mr-3"></i>
                        <h6 class="heading-6">
                            @lang('cooperation/frontend/shared.modals.add-measure.costs')
                        </h6>
                    </div>
                    @component('cooperation.frontend.layouts.components.form-group', [
                       'inputName' => "customMeasureApplicationsFormData.{$index}.costs.from",
                       'class' => 'w-1/2 pr-1',
                       'id' => 'new-measure-price-from',
                       'withInputSource' => false,
                   ])
                        <input class="form-input" wire:model="customMeasureApplicationsFormData.{{$index}}.costs.from"
                               id="new-measure-price-from" placeholder="@lang('default.from')">
                    @endcomponent
                    @component('cooperation.frontend.layouts.components.form-group', [
                        'inputName' => "customMeasureApplicationsFormData.{$index}.costs.to",
                        'class' => 'w-1/2 pl-1',
                        'id' => 'new-measure-price-to',
                        'withInputSource' => false,
                    ])
                        <input class="form-input" wire:model="customMeasureApplicationsFormData.{{$index}}.costs.to"
                               id="new-measure-price-to" placeholder="@lang('default.to')">
                    @endcomponent
                </div>
                <div class="w-full border border-gray fixed left-0"></div>
                <div class="flex flex-wrap justify-center mt-14">
                    <button wire:click="save({{$index}})" class="btn btn-purple w-full">
                        <i class="icon-xs icon-plus-purple mr-3"></i>
                        @if($loop->last)
                            @lang('cooperation/frontend/shared.modals.add-measure.save')
                        @else
                            @lang('cooperation/frontend/shared.modals.add-measure.update')
                        @endif
                    </button>
                </div>
            @endcomponent
        </div>
    @endforeach


</div>