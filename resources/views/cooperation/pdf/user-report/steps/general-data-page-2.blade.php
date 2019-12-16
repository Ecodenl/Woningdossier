<div class="question-answer-section">
    <p class="lead">{{__('pdf/user-report.general-data.resume-energy-saving-measures.title')}}</p>
    <table class="full-width">
        <thead>
        <tr>
            <th>{{__('pdf/user-report.general-data.resume-energy-saving-measures.table.planned-year')}}</th>
            <th>{{__('pdf/user-report.general-data.resume-energy-saving-measures.table.interested')}}</th>
            <th>{{__('pdf/user-report.general-data.resume-energy-saving-measures.table.measure')}}</th>
            <th>{{__('pdf/user-report.general-data.resume-energy-saving-measures.table.costs')}}</th>
            <th>{{__('pdf/user-report.general-data.resume-energy-saving-measures.table.savings')}}</th>
        </tr>
        </thead>
        <tbody>
        <?php
            $shownWarnings = [];
        ?>
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
                    @if(array_key_exists('warning', $advice) && !array_key_exists($advice['warning'], $shownWarnings))
                        {{--so we can check on key, if it already exists we dont show it--}}
                        <?php $shownWarnings[$advice['warning']] = null ?>
                    @endif
                @endforeach
            @endforeach
        @endforeach
        </tbody>
    </table>

    @foreach($shownWarnings as $warning => $nothing)
        <p style="color: darkgray">{{$warning}}</p>
        <br>
    @endforeach


    <p>{{__('pdf/user-report.general-data.resume-energy-saving-measures.text')}}</p>
</div>


@if(!\App\Helpers\Arr::isWholeArrayEmpty($userActionPlanAdviceComments))
    @include('cooperation.pdf.user-report.parts.measure-page.comments', [
        'comments' => $userActionPlanAdviceComments,
        'title' => __('pdf/user-report.general-data.comment-action-plan')
    ])
@endif

<?php
// if the total comment count exceeds a specific amount, we will create a new page otherwise it will overflow the footer..
// map the array to count the total comments, and then sum it.
$totalCommentCount = array_sum(
    array_map(function ($comment) {
        return strlen($comment);
    }, $userActionPlanAdviceComments)
);
?>

@if($totalCommentCount >= 2700)
    <div class="page-break"></div>
@endif

<div class="question-answer-section">
    <p class="lead">{{__('pdf/user-report.general-data.calculations-are-indicative.title')}}</p>
    <p>{{__('pdf/user-report.general-data.calculations-are-indicative.text')}}</p>
</div>

<div class="question-answer-section">
    <p class="lead">{{__('pdf/user-report.general-data.next-steps.title')}}</p>
    <p>
        {!! \App\Helpers\Translation::translate('pdf/user-report.general-data.next-steps.text-1', ['cooperation_name' => $userCooperation->name]) !!}
        @if(!empty($userCooperation->cooperation_email))
            {!! \App\Helpers\Translation::translate('pdf/user-report.general-data.next-steps.text-2', ['cooperation_email' => $userCooperation->cooperation_email]) !!}
        @endif
        @if(!empty($userCooperation->website_url))
            {!! \App\Helpers\Translation::translate('pdf/user-report.general-data.next-steps.text-3', ['website_url' => $userCooperation->website_url]) !!}
        @endif
    </p>
</div>