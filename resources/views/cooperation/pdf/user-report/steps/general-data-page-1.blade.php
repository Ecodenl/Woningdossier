<div id="general-data">

    <div class="question-answer-section">
        <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.front-page.address-info.title')}}</p>
        <div class="question-answer">
            <p class="w-300">{{\App\Helpers\Translation::translate('pdf/user-report.front-page.address-info.name')}}</p>
            <p>{{$user->getFullName()}}</p>
        </div>
        <div class="question-answer">
            <p class="w-300">{{\App\Helpers\Translation::translate('pdf/user-report.front-page.address-info.address')}}</p>
            <p>{{$building->street}} {{$building->number}} {{$building->extension}}</p>
        </div>
        <div class="question-answer">
            <p class="w-300">{{\App\Helpers\Translation::translate('pdf/user-report.front-page.address-info.zip-code-city')}}</p>
            <p>{{$building->postal_code}} {{$building->city}}</p>
        </div>
    </div>

    <div class="question-answer-section">
        <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.front-page.building-info.title')}}</p>
        <div class="question-answer">
            <p class="w-300">@lang('pdf/user-report.front-page.building-info.building-type')</p>
            <p>{{$buildingFeatures->buildingType->name}}</p>
        </div>
        <div class="question-answer">
            <p class="w-300">@lang('pdf/user-report.front-page.building-info.build-year')</p>
            <p>{{$buildingFeatures->build_year}}</p>
        </div>
        <div class="question-answer">
            <p class="w-300">@lang('pdf/user-report.front-page.building-info.surface')</p>
            <p>{{$buildingFeatures->surface}}</p>
        </div>
        <div class="question-answer">
            <p class="w-300">@lang('pdf/user-report.front-page.building-info.building-layers')</p>
            <p>{{$buildingFeatures->building_layers}}</p>
        </div>
        <div class="question-answer">
            <p class="w-300">@lang('pdf/user-report.front-page.building-info.roof-type')</p>
            <p>{{$buildingFeatures->roofType->name}}</p>
        </div>
        <div class="question-answer">
            <p class="w-300">@lang('pdf/user-report.front-page.building-info.current-energy-label')</p>
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
            <p class="w-300">@lang('pdf/user-report.front-page.building-info.monument')</p>
            <p>{{$possibleAnswers[$buildingFeatures->monument]}}</p>
        </div>
        <div class="question-answer">
            <p class="w-300">@lang('pdf/user-report.front-page.building-info.example-building')</p>
            <p>{{$building->exampleBuilding->name}}</p>
        </div>
    </div>

    <div class="question-answer-section">
        <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.front-page.usage-info.title')}}</p>
        @foreach($reportData['general-data']['user_energy_habits'] as $column => $value)
            <?php
                $translationForAnswer = $reportTranslations['general-data.user_energy_habits.'.$column];
            ?>
            <div class="question-answer">
                <p class="w-300">{{$translationForAnswer}}</p>
                <p>{{$value}}</p>
            </div>
        @endforeach
    </div>


    <div class="question-answer-section">
        <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.front-page.current-state.title')}}</p>
        <table class="full-width">
            <thead>
                <tr>
                    <th>{{\App\Helpers\Translation::translate('pdf/user-report.front-page.current-state.table.measure')}}</th>
                    <th>{{\App\Helpers\Translation::translate('pdf/user-report.front-page.current-state.table.present-current-situation')}}</th>
                    <th>{{\App\Helpers\Translation::translate('pdf/user-report.front-page.current-state.table.interested-in-improvement')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach(\Illuminate\Support\Arr::only($reportData['general-data'], ['element', 'service']) as $table => $data)
                    @foreach($data as $elementOrServiceId => $value)
                        @if (!is_array($value))
                        <?php
                            $translationForAnswer = $reportTranslations['general-data.'.$table.'.'.$elementOrServiceId];
                        ?>
                        <tr>
                            <td>{{$translationForAnswer}}</td>
                            <td>{{$value}}</td>
                            <td>{{$user->getInterestedType($table, $elementOrServiceId)->interest->name ?? 'x'}}</td>
                        </tr>
                        @endif
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="question-answer-section">
    <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.front-page.motivation')}}</p>
        @foreach($user->motivations as $motivation)
            <div class="question-answer">
                <p class="w-300">Motivatie {{$motivation->order}}</p>
                <p>{{$motivation->motivation->name}}</p>
            </div>
        @endforeach
    </div>

    <div class="question-answer-section">
        <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.front-page.comment-usage-building')}}</p>
        @if(array_key_exists('general-data', $commentsByStep))
            @foreach($commentsByStep['general-data'] as $inputSourceName => $commentsCategorizedUnderColumn)
                {{-- The column can be a category, this will be the case when the comment is stored under a catergory --}}
                @foreach($commentsCategorizedUnderColumn as $columnOrCategory => $comment)
                    <div class="question-answer">
                        @if(is_array($comment))
                            @foreach($comment as $column => $c)
                                <p class="w-300">{{$inputSourceName}} ({{$columnOrCategory}})</p>
                                <p>{{$c}}</p>
                            @endforeach
                        @else
                            <p class="w-300">{{$inputSourceName}}</p>
                            <p>{{$comment}}</p>
                        @endif
                    </div>
                @endforeach
            @endforeach
        @endif
    </div>
</div>

