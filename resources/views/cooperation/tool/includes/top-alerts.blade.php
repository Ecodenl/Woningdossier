<?php
    $changedToolSettings = $toolSettings->where('has_changed', true);
    $totalChangedToolSettings = $changedToolSettings->count();
    $toolSettingsLoopCount = 1;
    $isFillingToolForOtherBuilding = $user->isFillingToolForOtherBuilding()
?>

<div class="flex flex-wrap flex-row w-full">
    @if($isFillingToolForOtherBuilding && \App\Helpers\HoomdossierSession::isUserObserving())
        <div class="w-full sm:w-1/2">
            @component('cooperation.frontend.layouts.parts.alert', ['color' => 'blue-800', 'dismissible' => false, 'withBackground' => true])
                @lang('woningdossier.cooperation.tool.observing-tool', [
                    'first_name' => $buildingOwner->first_name,
                    'last_name' => $buildingOwner->last_name,
                    'input_source_name' => \App\Helpers\HoomdossierSession::getInputSourceValue(true)->name
                ])
            @endcomponent
        </div>
    @elseif($isFillingToolForOtherBuilding)
        <div class="w-full sm:w-1/2">
            @component('cooperation.frontend.layouts.parts.alert', ['color' => 'blue-800', 'dismissible' => false, 'withBackground' => true])
                @lang('woningdossier.cooperation.tool.filling-for', [
                    'first_name' => $buildingOwner->first_name,
                    'last_name' => $buildingOwner->last_name,
                    'input_source_name' => \App\Helpers\HoomdossierSession::getInputSourceValue(true)->name
                ])
            @endcomponent
        </div>
    @endif
    <div class="w-full @if($isFillingToolForOtherBuilding ) sm:w-1/2 @endif">
        @component('cooperation.frontend.layouts.parts.alert', ['color' => 'blue-800', 'dismissible' => false, 'withBackground' => true,'class' => 'building-notification'])
            @lang('woningdossier.cooperation.tool.current-building-address', [
                'street' => $building->street,
                'number' => $building->number.' '. $building->extension,
                'extension' => $building->extension,
                'zip_code' => $building->postal_code,
                'city' => $building->city
            ])
        @endcomponent
    </div>
</div>

{{--
    Alerts that will show when a resident (could be a admin role aswell but the feature is not implemented for a admin atm) is comparing his data to that
    From a other input source
 --}}
@if(\App\Helpers\HoomdossierSession::isUserComparingInputSources())
    <form id="copy-input-{{\App\Helpers\HoomdossierSession::getCompareInputSourceShort()}}" action="{{route('cooperation.import.copy')}}" method="post">
        <input type="hidden" name="input_source" value="{{\App\Helpers\HoomdossierSession::getCompareInputSourceShort()}}">
        @csrf
    </form>
    <div class="flex flex-row flex-wrap w-full">
        <div class="w-full">
            @component('cooperation.frontend.layouts.parts.alert', ['color' => 'blue-800', 'dismissible' => false, 'withBackground' => true, 'class' => 'input-source-notifications', 'closeClass' => 'close-input-source-notification'])
                <input type="hidden" class="input-source-short" value="{{\App\Helpers\HoomdossierSession::getCompareInputSourceShort()}}">
                <div class="flex flex-row flex-wrap w-full justify-between items-center">
                    <div class="w-full md:w-1/2">
                        <span>
                            @lang('woningdossier.cooperation.tool.is-user-comparing-input-sources', ['input_source_name' => \App\Models\InputSource::findByShort(\App\Helpers\HoomdossierSession::getCompareInputSourceShort())->name])
                        </span>
                    </div>
                    <div class="w-full md:w-1/2 text-right space-x-2">
                        <a onclick="$('#copy-input-{{\App\Helpers\HoomdossierSession::getCompareInputSourceShort()}}').submit()" class="btn btn-sm btn-green  mt-1">
                            @lang('my-account.import-center.index.copy-data', ['input_source_name' => \App\Models\InputSource::findByShort(\App\Helpers\HoomdossierSession::getCompareInputSourceShort())->name])
                        </a>
                        <a href="{{route('cooperation.my-account.import-center.set-compare-session', ['inputSourceShort' => \App\Helpers\HoomdossierSession::getInputSource(true)->short])}}" class="btn btn-sm btn-green  mt-1">
                            Stop vergelijking
                        </a>
                    </div>
                </div>
            @endcomponent
        </div>
    </div>
@elseif(!\App\Helpers\HoomdossierSession::isUserObserving())
{{--
    Alerts that will show when a resident / user is not comparing input sources
--}}
    <div class="flex flex-row flex-wrap w-full" id="input-source-notifications-row">
        @foreach($changedToolSettings as $i => $toolSetting)
            <?php ++$toolSettingsLoopCount; ?>
            <form id="copy-input-{{$toolSetting->id}}" action="{{route('cooperation.import.copy')}}" method="post">
                <input type="hidden" name="input_source" value="{{$toolSetting->changedInputSource->short}}">
                @csrf
            </form>

            {{--
                If there are more than one we will load all the input-source notifications, the building notification will be loaded on top of the page
             --}}
            <?php $width = 12 / $totalChangedToolSettings; $width = $width === 12 ? 'full' : $width; ?>
            <div class="w-full sm:w-{{$width}}">
                @component('cooperation.frontend.layouts.parts.alert', ['color' => 'green', 'dismissible' => true, 'withBackground' => true, 'class' => 'input-source-notifications', 'closeClass' => 'close-input-source-notification'])
                    <input type="hidden" class="input-source-short" value="{{$toolSetting->changedInputSource->short}}">
                    <div class="flex flex-row flex-wrap w-full justify-between items-center">
                        <div class="w-full md:w-1/2">
                            <span>
                                @lang('my-account.import-center.index.other-source-new', ['input_source_name' => $toolSetting->changedInputSource->name])
                            </span>
                        </div>
                        <div class="w-full md:w-1/2 text-right space-x-2">
                            <a href="{{route('cooperation.my-account.import-center.set-compare-session', ['inputSourceShort' => $toolSetting->changedInputSource->short])}}" class="btn btn-sm btn-green  mt-1">
                                @lang('my-account.import-center.index.show-differences')
                            </a>
                            <a onclick="$('#copy-input-{{$toolSetting->id}}').submit()" class="btn btn-sm btn-green mt-1">
                                @lang('my-account.import-center.index.copy-data', ['input_source_name' => $toolSetting->changedInputSource->name])
                            </a>
                        </div>
                    </div>
                @endcomponent
            </div>
        @endforeach
    </div>
@endif

@push('js')
    <script>
        $(document).ready(function () {

            // get the input source notifications
            let closeButtons = $('.close-input-source-notification');

            $(closeButtons).click(function () {
                // The input-source from the dismissed notification
                let dismissedInputSourceShort = $(this).closest('.input-source-notifications').first().find('.input-source-short').first().val();

                // send data
                $.ajax({
                    url: '{{route('cooperation.my-account.import-center.dismiss-notification')}}',
                    data: {input_source_short: dismissedInputSourceShort},
                    method: 'post'
                })
            });
        });
    </script>
@endpush