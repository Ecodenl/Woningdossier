<?php
$changedToolSettings = $toolSettings->where('has_changed', true);
$totalChangedToolSettings = $changedToolSettings->count();
$toolSettingsLoopCount = 1;

if (!isset($building)) {
    $building = \App\Models\Building::find(\App\Helpers\HoomdossierSession::getBuilding());
}
?>
{{--
    Alerts that will show if the user (prob a coach or other admin role) is filling the tool for a resident
--}}
@if (Auth::user()->isFillingToolForOtherBuilding())
    <div class="col-sm-6">
        @component('cooperation.tool.components.alert', ['alertType' => 'info', 'dismissible' => false])
            @lang('woningdossier.cooperation.tool.filling-for', [
                'first_name' => \App\Models\User::find(\App\Models\Building::find(\App\Helpers\HoomdossierSession::getBuilding())->user_id)->first_name,
                'last_name' => \App\Models\User::find(\App\Models\Building::find(\App\Helpers\HoomdossierSession::getBuilding())->user_id)->last_name,
                'input_source_name' => \App\Models\InputSource::find(\App\Helpers\HoomdossierSession::getInputSourceValue())->name
            ])
        @endcomponent
    </div>
    <div class="col-sm-6">
        @component('cooperation.tool.components.alert', ['alertType' => 'info', 'dismissible' => false])
            @lang('woningdossier.cooperation.tool.current-building-address', [
                'street' => $building->street,
                'number' => $building->number,
                'extension' => $building->extension,
                'zip_code' => $building->postal_code,
                'city' => $building->city
            ])
        @endcomponent
    </div>
{{--
    Alerts that will show when a resident (could be a admin role aswell but the feature is not implemented for a admin atm) is comparing his data to that
    From a other input source
 --}}
@elseif(\App\Helpers\HoomdossierSession::isUserComparingInputSources())
    <form id="copy-input-{{\App\Helpers\HoomdossierSession::getCompareInputSourceShort()}}" action="{{route('cooperation.import.copy')}}" method="post">
        <input type="hidden" name="input_source" value="{{\App\Helpers\HoomdossierSession::getCompareInputSourceShort()}}">
        {{csrf_field()}}
    </form>
    <div class="row">
        <div class="col-sm-6">
            @component('cooperation.tool.components.alert', ['alertType' => 'info', 'dismissible' => false, 'classes' => 'input-source-notifications'])
                <div class="row">
                    <div class="col-sm-6">
                        @lang('woningdossier.cooperation.tool.is-user-comparing-input-sources', ['input_source_name' => \App\Models\InputSource::findByShort(\App\Helpers\HoomdossierSession::getCompareInputSourceShort())->name])
                    </div>
                    <div class="col-sm-6">
                        <a onclick="$('#copy-input-{{\App\Helpers\HoomdossierSession::getCompareInputSourceShort()}}').submit()" class="btn btn-block btn-sm btn-primary pull-right">
                            @lang('woningdossier.cooperation.my-account.import-center.index.copy-data', ['input_source_name' => $toolSetting->inputSource->name])
                        </a>
                        <a href="{{route('cooperation.my-account.import-center.set-compare-session', ['inputSourceShort' => \App\Models\InputSource::find(\App\Helpers\HoomdossierSession::getInputSource())->short])}}" class="btn btn-block btn-sm btn-primary pull-right">
                            Stop vergelijking
                        </a>
                    </div>
                </div>
            @endcomponent
        </div>
        <div class="col-sm-6">
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
@elseif(\App\Helpers\HoomdossierSession::isUserNotComparingInputSources())
    @if($totalChangedToolSettings > 1)
        <div class="row">
            <div class="col-sm-12">
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
    @endif
    <div class="row" id="input-source-notifications-row">
        @foreach($changedToolSettings as $i => $toolSetting)
            <?php $toolSettingsLoopCount++ ?>
            <form id="copy-input-{{$toolSetting->id}}" action="{{route('cooperation.import.copy')}}" method="post">
                <input type="hidden" name="input_source" value="{{$toolSetting->inputSource->short}}">
                {{csrf_field()}}
            </form>

            @if($totalChangedToolSettings == 1)
                <div class="row">
                    <div class="col-sm-6">
                        @component('cooperation.tool.components.alert', ['alertType' => 'success', 'dismissible' => true, 'classes' => 'input-source-notifications'])
                            <div class="row">
                                <div class="col-sm-12">
                                    @lang('woningdossier.cooperation.my-account.import-center.index.other-source',
                                        ['input_source_name' => $toolSetting->inputSource->name
                                    ])
                                    <a onclick="$('#copy-input-{{$toolSetting->id}}').submit()" class="btn btn-sm btn-primary pull-right">
                                        @lang('woningdossier.cooperation.tool.general-data.coach-input.copy.title')
                                    </a>
                                </div>
                            </div>
                        @endcomponent
                    </div>
                    @if($totalChangedToolSettings == $totalChangedToolSettings)
                        <div class="col-sm-6">
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
                    @endif
                </div>
            @elseif($totalChangedToolSettings > 1)
                <div class="col-sm-6">
                    @component('cooperation.tool.components.alert', ['alertType' => 'success', 'dismissible' => true, 'classes' => 'input-source-notifications'])
                        <div class="row">
                            <div class="col-sm-12">
                                @lang('woningdossier.cooperation.my-account.import-center.index.other-source',
                                    ['input_source_name' => $toolSetting->inputSource->name
                                ])
                                <a onclick="$('#copy-input-{{$toolSetting->id}}').submit()" class="btn btn-sm btn-primary pull-right">
                                    @lang('woningdossier.cooperation.my-account.import-center.index.copy-data', ['input_source_name' => $toolSetting->inputSource->name])
                                </a>
                            </div>
                        </div>
                    @endcomponent
                </div>
            @endif
        @endforeach
    </div>
@endif

@push('js')
    <script>
        $(document).ready(function () {
            $('.input-source-notifications').on('closed.bs.alert', function () {

                // get the body
                var body = $('body');

                // all the building notificaitons
                var buildingNotification = body.find('.building-notification');

                // if the building notification parent has col 6, it was next to a input-source-notification.
                // now the input-source-noti is removed, so we give the building the col-12
                if (buildingNotification.parent().hasClass('col-sm-6')) {
                    buildingNotification.parent().removeClass('col-sm-6').addClass('col-sm-12')

                } else if(buildingNotification.parent().hasClass('col-sm-12')) {

                    var inputSourceNotificationsRow = $('#input-source-notifications-row');

                    // clone the building notification to the right column
                    inputSourceNotificationsRow.find('.col-sm-6').each(function (index, col) {
                        if ($(col).children().length === 0) {
                            buildingNotification.clone().appendTo(col);
                        }
                    });

                    // since we cloned the building notification we can remove it.
                    buildingNotification.remove();

                }

            });
        });
        
        function dismissInputSourceNotification() {
            console.log($(this));
        }
    </script>
@endpush