<div id="general-data">

    <div class="question-answer-section">
        <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.address-info.title')}}</p>
        <div class="question-answer">
            <p class="w-300">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.address-info.name')}}</p>
            <p>{{$user->getFullName()}}</p>
        </div>
        <div class="question-answer">
            <p class="w-300">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.address-info.address')}}</p>
            <p>{{$building->street}} {{$building->number}} {{$building->extension}}</p>
        </div>
        <div class="question-answer">
            <p class="w-300">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.address-info.zip-code-city')}}</p>
            <p>{{$building->postal_code}} {{$building->city}}</p>
        </div>
    </div>

    {{-- Not in order in the reportData, easier and more readable to do it like this. --}}
    <div class="question-answer-section">
        <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.building-info.title')}}</p>

        <div class="question-answer">
            <p class="w-300">@lang('pdf/user-report.general-data.building-info.building-type')</p>
            <p>{{$buildingFeatures->buildingType->name}}</p>
        </div>
        <div class="question-answer">
            <p class="w-300">@lang('pdf/user-report.general-data.building-info.build-year')</p>
            <p>{{$buildingFeatures->build_year}}</p>
        </div>
        <div class="question-answer">
            <p class="w-300">@lang('pdf/user-report.general-data.building-info.surface')</p>
            <p>{{$buildingFeatures->surface}} {{\App\Helpers\Hoomdossier::getUnitForColumn('surface')}}</p>
        </div>
        <div class="question-answer">
            <p class="w-300">@lang('pdf/user-report.general-data.building-info.building-layers')</p>
            <p>{{$buildingFeatures->building_layers}}</p>
        </div>
        <div class="question-answer">
            <p class="w-300">@lang('pdf/user-report.general-data.building-info.roof-type')</p>
            <p>{{$buildingFeatures->roofType->name}}</p>
        </div>
        <div class="question-answer">
            <p class="w-300">@lang('pdf/user-report.general-data.building-info.current-energy-label')</p>
            <p>{{$buildingFeatures->energyLabel->name}}</p>
        </div>
        <?php
            $possibleAnswers = [
                1 => \App\Helpers\Translation::translate('general.options.yes.title'),
                2 => \App\Helpers\Translation::translate('general.options.no.title'),
                0 => \App\Helpers\Translation::translate('general.options.unknown.title'),
            ];
        ?>
        <div class="question-answer">
            <p class="w-300">@lang('pdf/user-report.general-data.building-info.monument')</p>
            <p>{{$possibleAnswers[$buildingFeatures->monument]}}</p>
        </div>
        <div class="question-answer">
            <p class="w-300">@lang('pdf/user-report.general-data.building-info.example-building')</p>
            <p>{{$building->exampleBuilding->name}}</p>
        </div>
    </div>

    <div class="question-answer-section">
        <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.usage-info.title')}}</p>

        <table class="full-width">
            <tbody>
            @foreach($reportData['general-data']['user_energy_habits'] as $column => $value)
                <?php
                    $translationForAnswer = $reportTranslations['general-data.user_energy_habits.'.$column];
                ?>

                <tr style="border-bottom: 0px;">
                    <td class="w-300" style="border-top: 0px;">{{$translationForAnswer}}</td>
                    <td style="border-top: 0px;">{{$value}} {{\App\Helpers\Hoomdossier::getUnitForColumn($column)}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>


    {{-- Current state of the building, elements and services with its interest level. --}}
    <div class="question-answer-section">
        <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.current-state.title')}}</p>
        <table class="full-width">
            <thead>
                <tr>
                    <th>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.current-state.table.measure')}}</th>
                    <th>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.current-state.table.present-current-situation')}}</th>
                    <th>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.current-state.table.interested-in-improvement')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach(\Illuminate\Support\Arr::only($reportData['general-data'], ['element', 'service']) as $table => $data)
                    @foreach($data as $elementOrServiceId => $value)
                        @if (!is_array($value))
                            <?php
                                $translationForAnswer = $reportTranslations['general-data.'.$table.'.'.$elementOrServiceId];
                            ?>
                            <tr class="border-bottom">
                                <td>{{$translationForAnswer}}</td>
                                <td>{{$value}}</td>
                                <td>{{$user->getInterestedType($table, $elementOrServiceId, $inputSource)->interest->name ?? 'x'}}</td>
                            </tr>
                        @endif
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

</div>

