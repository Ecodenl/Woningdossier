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
        {{--
            todo: these are just all the action plan advices, find out if these need to be the exact same as my plan 'Uw persoonlijke meerjarenonderhoudsplan'
        --}}
        @foreach($userActionPlanAdvices as $year => $advices)
            @foreach($advices as $measureName => $adviceData)
                @foreach($adviceData as $advice)
            <?php
//                 determine which year we will use
//                $year = isset($userActionPlanAdvice->planned_year) ? $userActionPlanAdvice->planned_year : $userActionPlanAdvice->year;
//                if (is_null($year)) {
//                    $year = $userActionPlanAdvice->getAdviceYear() ?? __('woningdossier.cooperation.tool.my-plan.no-year');
//                }
            ?>
            <tr>
                <td>{{$year}}</td>
                <td>{{$advice['interested'] ? 'Ja' : 'Nee'}}</td>
                <td>{{$measureName}}</td>
                <td>{{$advice['costs']}}</td>
                <td>{{$advice['savings_money']}}</td>
            </tr>
            @endforeach
            @endforeach
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
    <p>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.next-steps.text', ['cooperation_name' => 'Hoom'])}}</p>
</div>

<div class="question-answer-section">
    <h2>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.attachment.title')}}</h2>
    <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.attachment.lead')}}</p>
    <p>{!!\App\Helpers\Translation::translate('pdf/user-report.general-data.attachment.text')!!}</p>
</div>