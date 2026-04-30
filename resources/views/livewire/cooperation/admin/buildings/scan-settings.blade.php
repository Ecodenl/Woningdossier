<div class="w-full flex flex-wrap" x-data="{ selectedScan: '{{ $selectedScan }}' }">
    {{-- Scan type select --}}
    <div class="form-group w-full lg:w-1/2 lg:pr-3">
        <div class="form-header">
            <label class="form-label max-w-16/20">
                @lang('cooperation/admin/buildings.show.scan-availability.title')
            </label>
        </div>
        <div class="w-full">
            <p class="text-sm text-gray-600 mb-2">
                @lang('cooperation/admin/buildings.show.scan-availability.description')
            </p>
            <div wire:ignore>
                @component('cooperation.frontend.layouts.components.alpine-select')
                    <select class="form-input hidden" id="scan-type-select" x-model="selectedScan"
                            x-on:change="$wire.updateScanType($event.target.value)">
                        @foreach($mapping as $type => $typeTranslation)
                            <option value="{{ $type }}"
                                    @if($selectedScan === $type) selected @endif
                                    @if(isset($disabledOptions[$type])) disabled @endif
                            >{{ $typeTranslation }}</option>
                        @endforeach
                    </select>
                @endcomponent
            </div>

            @if(! empty($disabledOptions))
                <p class="text-sm text-gray-500 mt-1">
                    {{ collect($disabledOptions)->first() }}
                </p>
            @endif
        </div>
    </div>

    {{-- Small measures checkboxes --}}
    <div class="form-group w-full lg:w-1/2 lg:pl-3">
        <div class="form-header">
            <label class="form-label max-w-16/20">
                @lang('cooperation/admin/buildings.show.small-measures.title')
            </label>
        </div>
        <div class="w-full">
            <p class="text-sm text-gray-600 mb-2">
                @lang('cooperation/admin/buildings.show.small-measures.description')
            </p>

            @foreach(['quick-scan' => __('cooperation/admin/cooperation/cooperation-admin/scans.form.small-measures.quick-scan'), 'lite-scan' => __('cooperation/admin/cooperation/cooperation-admin/scans.form.small-measures.lite-scan')] as $scanShort => $scanName)
                @php
                    $isLiteScan = $scanShort === \App\Models\Scan::LITE;
                    $cooperationEnabled = \App\Helpers\SmallMeasuresSettingHelper::isEnabledForCooperation($cooperation, \App\Models\Scan::findByShort($scanShort));
                @endphp
                <div class="flex items-center mb-3"
                     x-show="selectedScan === '{{ $scanShort }}' || selectedScan === 'both-scans'"
                     x-cloak>
                    <label class="flex items-center {{ $isLiteScan ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }}">
                        <input type="checkbox"
                               class="form-checkbox h-5 w-5 text-green-600"
                               @if($isLiteScan)
                                   checked disabled
                               @else
                                   wire:click="toggleSmallMeasures('{{ $scanShort }}', $event.target.checked)"
                                   @checked($smallMeasuresEnabled[$scanShort] ?? false)
                               @endif>
                        <span class="ml-2">
                            {{ $scanName }}: @lang('cooperation/admin/cooperation/cooperation-admin/scans.form.small-measures.label')
                            @if($isLiteScan)
                                <span class="text-sm text-gray-500 italic">
                                    (@lang('cooperation/admin/cooperation/cooperation-admin/scans.form.small-measures.always-required'))
                                </span>
                            @endif
                        </span>
                    </label>
                </div>
            @endforeach
        </div>
    </div>
</div>
