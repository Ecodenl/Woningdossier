@php
    $disabled = \App\Helpers\HoomdossierSession::isUserObserving();
@endphp
<div class="w-full grid grid-rows-2 grid-cols-4 grid-flow-row justify-items-center">
    @foreach($cooperationMeasureApplicationsFormData as $index => $customMeasureApplicationFormData)
        <div class="checkbox-wrapper media-wrapper">
            <input type="checkbox" id="cooperation-measure-{{$index}}" value="{{ $index }}"
                   wire:model="selectedCooperationMeasureApplications"
                   @if($disabled) disabled="disabled" @endif
            >
            <label for="cooperation-measure-{{$index}}">
                <span class="media-icon-wrapper">
                    <i class="{{ $customMeasureApplicationFormData['extra']['icon'] }}"></i>
                </span>
                <span class="checkmark"></span>
                <span>{{ $customMeasureApplicationFormData['name'] }}</span>
            </label>
        </div>
    @endforeach
    @if($type === \App\Helpers\Models\CooperationMeasureApplicationHelper::SMALL_MEASURE)
        @foreach($customMeasureApplicationsFormData as $index => $customMeasureApplicationFormData)
            <div class="@if($loop->last) add-option-wrapper @else checkbox-wrapper @endif media-wrapper"
                 @if(! $disabled) x-data="modal()" @endif>
                @if(! $loop->last)
                    <input type="checkbox" id="custom-measure-{{$index}}" value="{{ $index }}"
                           wire:model="selectedCustomMeasureApplications"
                           @if($disabled) disabled="disabled" @endif>

                @endif
                <label for="custom-measure-{{$index}}" x-on:click="toggle()">
                    <span class="media-icon-wrapper">
                        <i class="@if($loop->last) icon-plus-circle @else {{$customMeasureApplicationFormData['extra']['icon']}} @endif"></i>
                    </span>
                    @if(! $loop->last)
                        <span class="checkmark" x-on:click.stop></span>
                        <span>{{$customMeasureApplicationFormData['name']}}</span>
                    @else
                        <span>@lang('cooperation/frontend/tool.form.add-option')</span>
                    @endif
                </label>
                @if(! $disabled)
                    @include('livewire.cooperation.frontend.layouts.parts.custom-measure-modal')
                @endif
            </div>
        @endforeach
    @endif
</div>