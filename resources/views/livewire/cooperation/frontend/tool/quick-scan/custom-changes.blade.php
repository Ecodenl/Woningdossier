<div class="w-full grid grid-rows-2 grid-cols-4 grid-flow-row justify-items-center">
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
    @foreach($customMeasureApplications as $index => $customMeasureApplication)
        <div class="@if($loop->last) add-option-wrapper @else checkbox-wrapper @endif media-wrapper" x-data="modal()">
            @if(!$loop->last)
                <input type="checkbox" id="custom-measure-{{$index}}" name="changes" value="attic-room">
            @endif
            <label for="custom-measure-{{$index}}" x-on:click="toggle()">
                <span class="media-icon-wrapper">
                    <i class="@if($loop->last) icon-plus-circle @else{{$customMeasureApplication['extra']['icon']}} @endif"></i>
                </span>
                @if(!$loop->last)
                    <span class="checkmark"></span>
                @else
                    <span>@lang('cooperation/frontend/tool.form.add-option')</span>
                @endif

                <span>{{$customMeasureApplication['name']}}</span>
            </label>
            @component('cooperation.frontend.layouts.components.modal', ['header' => __('livewire/cooperation/frontend/tool/quick-scan/custom-changes.name.title')])
                <div class="flex flex-wrap mb-5">
                    @component('cooperation.frontend.layouts.components.form-group', [
                       'inputName' => 'new_measure.subject',
                       'class' => 'w-full -mt-4 mb-4',
                       'id' => 'new-measure-subject',
                       'withInputSource' => false,
                   ])
                        <input class="form-input" wire:model="customMeasureApplications.{{$index}}.name" id="new-measure-subject"

                               placeholder="@lang('livewire/cooperation/frontend/tool/quick-scan/custom-changes.na')">
                    @endcomponent
                    <div class="w-full flex items-center">
                        <i class="icon-sm icon-info mr-3"></i>
                        <h6 class="heading-6">
                            @lang('livewire/cooperation/frontend/tool/quick-scan/custom-changes.name.title')
                        </h6>
                    </div>
                    @component('cooperation.frontend.layouts.components.form-group', [
                       'inputName' => 'new_measure.price.from',
                       'class' => 'w-1/2 pr-1',
                       'id' => 'new-measure-price-from',
                       'withInputSource' => false,
                   ])
                        <input class="form-input" name="new_measure[price][from]" id="new-measure-price-from"
                               placeholder="van">
                    @endcomponent
                    @component('cooperation.frontend.layouts.components.form-group', [
                        'inputName' => 'new_measure.price.to',
                        'class' => 'w-1/2 pl-1',
                        'id' => 'new-measure-price-to',
                        'withInputSource' => false,
                    ])
                            <input class="form-input" name="new_measure[price][to]" id="new-measure-price-to" placeholder="tot">
                    @endcomponent
                </div>
                <div class="w-full border border-gray fixed left-0"></div>
                <div class="flex flex-wrap justify-center mt-14">
                    <button x-on:click="toggle()" wire:click="save" class="btn btn-purple w-full">
                        <i class="icon-xs icon-plus-purple mr-3"></i>
                        @lang('livewire/cooperation/frontend/tool/quick-scan/custom-changes.save')
                    </button>
                </div>
            @endcomponent
        </div>
    @endforeach


</div>