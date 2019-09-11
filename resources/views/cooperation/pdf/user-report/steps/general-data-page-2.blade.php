@include('cooperation.pdf.user-report.parts.comments', ['stepSlug' => 'general-data'])

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
        @foreach($userActionPlanAdvices as $year => $advices)
            @foreach($advices as $adviceData)
                @foreach($adviceData as $advice)
                <tr class="border-bottom">
                    <?php
                        // if its a string, the $year contains 'geen jaartal'
                        is_string($year) ? $year = \Carbon\Carbon::now()->year : $year;
                    ?>
                    <td align="center">{{$year}}</td>
                    <td align="center">{{$advice['interested'] ? 'Ja' : 'Nee'}}</td>
                    <td>{{$advice['measure']}}</td>
                    <td align="right">{{\App\Helpers\NumberFormatter::format($advice['costs'])}}</td>
                    <td align="right">{{\App\Helpers\NumberFormatter::format($advice['savings_money'])}}</td>
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
    @foreach($userActionPlanAdviceComments as $userActionPlanAdviceComment)
        <div class="question-answer">
            <p class="w-100">{{$userActionPlanAdviceComment->inputSource->name}}</p>
            <p>{{$userActionPlanAdviceComment->comment}}</p>
        </div>
    @endforeach
</div>

<div class="question-answer-section">
    <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.calculations-are-indicative.title')}}</p>
    <p>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.calculations-are-indicative.text')}}</p>
</div>

<div class="question-answer-section">
    <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.next-steps.title')}}</p>
    <p>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.next-steps.text', ['cooperation_name' => $userCooperation->name])}}</p>
</div>
