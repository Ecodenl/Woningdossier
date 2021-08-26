<div class="question-answer-section">
    <p class="lead">{{__('pdf/user-report.general-data.resume-energy-saving-measures.title')}}</p>
    <table class="full-width">
        <thead class="no-background">
        <tr>
            <th>{{__('pdf/user-report.general-data.resume-energy-saving-measures.table.planned-year')}}</th>
            <th>{{__('pdf/user-report.general-data.resume-energy-saving-measures.table.measure')}}</th>
            <th>{!! __('pdf/user-report.general-data.resume-energy-saving-measures.table.costs') !!}</th>
            <th>{!! __('pdf/user-report.general-data.resume-energy-saving-measures.table.savings') !!}</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $shownWarnings = [];
        ?>
        @foreach($userActionPlanAdvices as $year => $advices)
            @foreach($advices as $adviceData)
                @foreach($adviceData as $advice)
                    <tr>
                        <td>{{$year}}</td>
                        <td>{{$advice['measure']}}</td>
                        <td>{{\App\Helpers\NumberFormatter::format($advice['costs']['from'] ?? 0)}}</td>
                        <td>{{\App\Helpers\NumberFormatter::format($advice['savings_money'])}}</td>
                    </tr>
                    @if(array_key_exists('warning', $advice) && is_string($advice['warning']) && !array_key_exists($advice['warning'], $shownWarnings))
                        {{--so we can check on key, if it already exists we dont show it--}}
                        <?php $shownWarnings[$advice['warning']] = null; ?>
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
    <div class="question-answer-section">
        <p class="lead">@lang('pdf/user-report.general-data.comment-action-plan')</p>
        @foreach($userActionPlanAdviceComments as $inputSourceName => $comment)
            {{-- The column can be a category, this will be the case when the comment is stored under a catergory--}}
            <p class="sub-lead" style="margin-top: 25px">@lang('pdf/user-report.general-data.comment-action-plan-by', ['name' => $inputSourceName])</p>
            <p style="word-wrap: break-word !important;">{!!  nl2br($comment, '<br>')!!}</p>
        @endforeach
    </div>
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