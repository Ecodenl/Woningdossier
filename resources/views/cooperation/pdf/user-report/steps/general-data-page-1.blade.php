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

    <div class="question-answer-section">
        <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.building-info.title')}}</p>
        <div class="question-answer">
            <p class="w-300">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.address-info.zip-code-city')}}</p>
            <p></p>
        </div>
    </div>

    <div class="question-answer-section">
        <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.usage-info.title')}}</p>
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
    <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.motivation')}}</p>
        @foreach($user->motivations as $motivation)
            <div class="question-answer">
                <p class="w-300">Motivatie {{$motivation->order}}</p>
                <p>{{$motivation->motivation->name}}</p>
            </div>
        @endforeach
    </div>

    <div class="question-answer-section">
        <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.comment-usage-building')}}</p>
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

