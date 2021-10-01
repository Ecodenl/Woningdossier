<div class="question-answer-section">
    <p class="lead">{{__('pdf/user-report.general-data.resume-energy-saving-measures.title')}}</p>
    <table class="full-width">
        <thead class="no-background">
        <tr>
            <th>{{__('pdf/user-report.general-data.resume-energy-saving-measures.table.measure')}}</th>
            <th>{!! __('pdf/user-report.general-data.resume-energy-saving-measures.table.costs') !!}</th>
            <th>{!! __('pdf/user-report.general-data.resume-energy-saving-measures.table.savings') !!}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($userActionPlanAdvices as $userActionPlanAdvice)
            @php
                $name = null;
                if ($userActionPlanAdvice->userActionPlanAdvisable instanceof \Illuminate\Database\Eloquent\Model) {
                    $name = $userActionPlanAdvice->userActionPlanAdvisable->name ?? $userActionPlanAdvice->userActionPlanAdvisable->measure_name;
                } else {
                    \Illuminate\Support\Facades\Log::debug("User action plan advise its advisable does not exist; user_action_plan_advice_id: {$userActionPlanAdvice->id}");
                }
            @endphp
            @if(!is_null($name))
            <tr>
                <td>{{$name}}</td>
                <td>{{\App\Helpers\NumberFormatter::format($userActionPlanAdvice->costs['from'] ?? 0)}}</td>
                <td>{{\App\Helpers\NumberFormatter::format($userActionPlanAdvice->savings_money)}}</td>
            </tr>
            @endif
        @endforeach

        @php
            $electricityUsage = $userEnergyHabit->amount_electricity;
            $totalElectricitySaving = $userActionPlanAdvices->sum('savings_electricity');

            $currentCosts = $electricityUsage * \App\Helpers\Kengetallen::EURO_SAVINGS_ELECTRICITY;
            $expectedCosts = ($electricityUsage - $totalElectricitySaving) * \App\Helpers\Kengetallen::EURO_SAVINGS_ELECTRICITY;
        @endphp
        <tr>
            <td></td>
        </tr>
        <tr>
            <td>
                <p class="sub-lead">@lang('pdf/user-report.general-data.resume-energy-saving-measures.current-annual-energy-costs')</p>
            </td>
            <td>{{\App\Helpers\NumberFormatter::format($currentCosts)}} €</td>
            <td></td>
        </tr>
        <tr>
            <td>
                <p class="sub-lead">@lang('pdf/user-report.general-data.resume-energy-saving-measures.expected-annual-energy-costs')</p>
            </td>
            <td>{{\App\Helpers\NumberFormatter::format($expectedCosts)}} €</td>
            <td></td>
        </tr>
        </tbody>
    </table>
</div>


@if(!\App\Helpers\Arr::isWholeArrayEmpty($userActionPlanAdviceComments))
    <div class="question-answer-section">
        <p class="lead">@lang('pdf/user-report.general-data.comment-action-plan')</p>
        @foreach($userActionPlanAdviceComments as $inputSourceName => $comment)
            {{-- The column can be a category, this will be the case when the comment is stored under a catergory--}}
            <p class="sub-lead"
               style="margin-top: 25px">@lang('pdf/user-report.general-data.comment-action-plan-by', ['name' => $inputSourceName])</p>
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