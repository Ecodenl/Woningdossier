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
                @foreach(array_only($reportData['general-data'], ['element', 'service']) as $table => $data)
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
    <p>data</p>
    </div>

    <div class="question-answer-section">
        <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.comment-usage-building')}}</p>
        <p>data</p>
    </div>

    <div class="question-answer-section">
        <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.resume-energy-saving-measures.title')}}</p>
        <table class="full-width">
            <thead>
                <tr>
                    <th>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.resume-energy-saving-measures.table.planned-year')}}</th>
                    <th>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.resume-energy-saving-measures.table.interested')}}</th>
                    <th>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.resume-energy-saving-measures.table.measure')}}</th>
                    <th>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.resume-energy-saving-measures.table.costs')}}</th>
                    <th>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.resume-energy-saving-measures.table.savings')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($userActionPlanAdvices as $userActionPlanAdvice)
                <tr>
                    <td>{{$userActionPlanAdvice->getAdviceYear()}}</td>
                    <td>{{$userActionPlanAdvice->planned ? 'Ja' : 'Nee'}}</td>
                    <td>{{$userActionPlanAdvice->measureApplication->measure_name}}</td>
                    <td>{{$userActionPlanAdvice->costs}}</td>
                    <td>{{$userActionPlanAdvice->savings_money}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.resume-energy-saving-measures.text')}}</p>
    </div>


    <div class="question-answer-section">
        <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.comment-action-plan')}}</p>
        <p>data</p>
    </div>

    <div class="question-answer-section">
        <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.calculations-are-indicative.title')}}</p>
        <p>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.calculations-are-indicative.text')}}</p>
    </div>

    <div class="question-answer-section">
        <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.next-steps.title')}}</p>
        <p>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.next-steps.text', ['cooperation_name' => strtolower($cooperation->name)])}}</p>
    </div>

    <div class="question-answer-section">
        <h2>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.attachment.title')}}</h2>
        <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.attachment.lead')}}</p>
        <p>{!!\App\Helpers\Translation::translate('pdf/user-report.general-data.attachment.text')!!}</p>
    </div>


{{--    <div class="container bg-white" id="user-info">--}}
{{--        <h1>{{$user->getFullName()}}</h1>--}}
{{--        <h1>{{$building->street}} {{$building->number}} {{$building->extension}}</h1>--}}
{{--        <h1>{{$building->postal_code}} {{$building->city}}</h1>--}}
{{--    </div>--}}
{{----}}
{{--    <div id="img-front-page">--}}
{{--        <img src="{{asset('images/pdf-main-images.jpg')}}">--}}
{{--    </div>--}}
{{----}}
{{--    <div class="page-footer bg-white" id="intro">--}}
{{--        <h2 class="text-uppercase">@lang('pdf/user-report.front-page.intro.title')</h2>--}}
{{--        <p>@lang('pdf/user-report.front-page.intro.text')</p>--}}
{{--    </div>--}}

</div>

