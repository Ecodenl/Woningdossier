<?php
$changedToolSettings = $toolSettings->where('has_changed', true);
$totalChangedToolSettings = $changedToolSettings->count();
$toolSettingsLoopCount = 1;

if (! isset($building)) {
    $building = \App\Models\Building::find(\App\Helpers\HoomdossierSession::getBuilding());
}
?>

<div class="row">
    @if (Auth::user()->isFillingToolForOtherBuilding() && \App\Helpers\HoomdossierSession::isUserObserving())
        <div class="col-sm-6">
            @component('cooperation.tool.components.alert', ['alertType' => 'info', 'dismissible' => false])
                @lang('woningdossier.cooperation.tool.observing-tool', [
                    'first_name' => \App\Models\User::find(\App\Models\Building::find(\App\Helpers\HoomdossierSession::getBuilding())->user_id)->first_name,
                    'last_name' => \App\Models\User::find(\App\Models\Building::find(\App\Helpers\HoomdossierSession::getBuilding())->user_id)->last_name,
                    'input_source_name' => \App\Models\InputSource::find(\App\Helpers\HoomdossierSession::getInputSourceValue())->name
                ])
            @endcomponent
        </div>
    @elseif(Auth::user()->isFillingToolForOtherBuilding())
        <div class="col-sm-6">
            @component('cooperation.tool.components.alert', ['alertType' => 'info', 'dismissible' => false])
                @lang('woningdossier.cooperation.tool.filling-for', [
                    'first_name' => \App\Models\User::find(\App\Models\Building::find(\App\Helpers\HoomdossierSession::getBuilding())->user_id)->first_name,
                    'last_name' => \App\Models\User::find(\App\Models\Building::find(\App\Helpers\HoomdossierSession::getBuilding())->user_id)->last_name,
                    'input_source_name' => \App\Models\InputSource::find(\App\Helpers\HoomdossierSession::getInputSourceValue())->name
                ])
            @endcomponent
        </div>
    @endif
    <div class="@if(Auth::user()->isFillingToolForOtherBuilding())col-sm-6 @else col-sm-12 @endif">
        @component('cooperation.tool.components.alert', ['alertType' => 'info', 'dismissible' => false, 'classes' => 'building-notification'])
            @lang('woningdossier.cooperation.tool.current-building-address', [
                'street' => $building->street,
                'number' => $building->number,
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
        {{csrf_field()}}
    </form>
    <div class="row">
        <div class="col-sm-12">
            @component('cooperation.tool.components.alert', ['alertType' => 'info', 'dismissible' => false, 'classes' => 'input-source-notifications'])
                <input type="hidden" class="input-source-short" value="{{\App\Helpers\HoomdossierSession::getCompareInputSourceShort()}}">
                <div class="row">
                    <div class="col-sm-6">
                        @lang('woningdossier.cooperation.tool.is-user-comparing-input-sources', ['input_source_name' => \App\Models\InputSource::findByShort(\App\Helpers\HoomdossierSession::getCompareInputSourceShort())->name])
                    </div>
                    <div class="col-sm-6">
                        <a onclick="$('#copy-input-{{\App\Helpers\HoomdossierSession::getCompareInputSourceShort()}}').submit()" class="btn btn-block btn-sm btn-primary pull-right">
                            @lang('woningdossier.cooperation.my-account.import-center.index.copy-data', ['input_source_name' => \App\Models\InputSource::findByShort(\App\Helpers\HoomdossierSession::getCompareInputSourceShort())->name])
                        </a>
                        <a href="{{route('cooperation.my-account.import-center.set-compare-session', ['inputSourceShort' => \App\Models\InputSource::find(\App\Helpers\HoomdossierSession::getInputSource())->short])}}" class="btn btn-block btn-sm btn-primary pull-right">
                            Stop vergelijking
                        </a>
                    </div>
                </div>
            @endcomponent
        </div>
    </div>
@else
{{--
    Alerts that will show when a resident / user is not comparing input sources
--}}
    @if(!\App\Helpers\HoomdossierSession::isUserObserving())
    <div class="row" id="input-source-notifications-row">
        @foreach($changedToolSettings as $i => $toolSetting)
            <?php ++$toolSettingsLoopCount; ?>
            <form id="copy-input-{{$toolSetting->id}}" action="{{route('cooperation.import.copy')}}" method="post">
                <input type="hidden" name="input_source" value="{{$toolSetting->changedInputSource->short}}">
                {{csrf_field()}}
            </form>

            {{--
                If there are more than one we will load all the input-source notifications, the building notification will be loaded on top of the page
             --}}
            <?php $col = 12 / $totalChangedToolSettings; ?>
            <div class="col-sm-{{$col}}">
                @component('cooperation.tool.components.alert', ['alertType' => 'success', 'dismissible' => true, 'classes' => 'input-source-notifications'])
                    <input type="hidden" class="input-source-short" value="{{$toolSetting->changedInputSource->short}}">
                    <div class="row">
                        <div class="col-sm-12">
                            <a href="{{route('cooperation.my-account.import-center.set-compare-session', ['inputSourceShort' => $toolSetting->changedInputSource->short])}}" class="btn btn-sm btn-primary pull-right">
                                @lang('woningdossier.cooperation.my-account.import-center.index.show-differences')
                            </a>
                            <a onclick="$('#copy-input-{{$toolSetting->id}}').submit()" class="btn btn-sm btn-primary pull-right">
                                @lang('woningdossier.cooperation.my-account.import-center.index.copy-data', ['input_source_name' => $toolSetting->changedInputSource->name])
                            </a>
                            @lang('woningdossier.cooperation.my-account.import-center.index.other-source-new',
                                ['input_source_name' => $toolSetting->changedInputSource->name
                            ])
                        </div>
                    </div>
                @endcomponent
            </div>
        @endforeach
    </div>
    @endif
@endif

@push('js')
    <script>
        $(document).ready(function () {

            // get the input source notifications
            var inputSourceNotification = $('.input-source-notifications');
            var dismissedInputSourceNotification;

            // set the dismissedInputSource notification on close
            inputSourceNotification.on('close.bs.alert', function () {
                dismissedInputSourceNotification = $(this);
            });

            // now do some magic if the alert is closed.
            inputSourceNotification.on('closed.bs.alert', function () {

                // the input-source from the dismissed notification
                var dismissedInputSourceShort = dismissedInputSourceNotification.find('.input-source-short').val();

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