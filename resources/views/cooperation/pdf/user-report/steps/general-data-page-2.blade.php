<div class="question-answer-section">
    <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.front-page.resume-energy-saving-measures.title')}}</p>
    <table class="full-width">
        <thead>
        <tr>
            <th>{{\App\Helpers\Translation::translate('pdf/user-report.front-page.resume-energy-saving-measures.table.planned-year')}}</th>
            <th>{{\App\Helpers\Translation::translate('pdf/user-report.front-page.resume-energy-saving-measures.table.interested')}}</th>
            <th>{{\App\Helpers\Translation::translate('pdf/user-report.front-page.resume-energy-saving-measures.table.measure')}}</th>
            <th>{{\App\Helpers\Translation::translate('pdf/user-report.front-page.resume-energy-saving-measures.table.costs')}}</th>
            <th>{{\App\Helpers\Translation::translate('pdf/user-report.front-page.resume-energy-saving-measures.table.savings')}}</th>
        </tr>
        </thead>
        <tbody>
        {{--
        todo: these are just all the action plan advices, find out if these need to be the exact same as my plan 'Uw persoonlijke meerjarenonderhoudsplan'
        --}}
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
    <p>{{\App\Helpers\Translation::translate('pdf/user-report.front-page.resume-energy-saving-measures.text')}}</p>
</div>


<div class="question-answer-section">
    <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.front-page.comment-action-plan')}}</p>
    <p>data</p>
</div>

<div class="question-answer-section">
    <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.front-page.calculations-are-indicative.title')}}</p>
    <p>{{\App\Helpers\Translation::translate('pdf/user-report.front-page.calculations-are-indicative.text')}}</p>
</div>

<div class="question-answer-section">
    <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.front-page.next-steps.title')}}</p>
    <p>{{\App\Helpers\Translation::translate('pdf/user-report.front-page.next-steps.text', ['cooperation_name' => 'Hoom'])}}</p>
</div>

<div class="question-answer-section">
    <h2>{{\App\Helpers\Translation::translate('pdf/user-report.front-page.attachment.title')}}</h2>
    <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.front-page.attachment.lead')}}</p>
    <p>{!!\App\Helpers\Translation::translate('pdf/user-report.front-page.attachment.text')!!}</p>
</div>