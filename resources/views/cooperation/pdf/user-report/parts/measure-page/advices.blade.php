@isset($advices['maintenance'][$stepShort][$subStepShort])
    <div class="question-answer-section">
        <div class="measures">
            <p class="lead w-380">
                {{__('pdf/user-report.measure-pages.measures.maintenance.title')}}
            </p>
            <p class="lead w-150">
                {{__('pdf/user-report.measure-pages.measures.maintenance.costs')}}
            </p>
            <p class="lead w-150">
                {{__('pdf/user-report.measure-pages.measures.maintenance.year')}}
            </p>
        </div>
        @foreach($advices['maintenance'][$stepShort] as $userActionPlanAdvice)
            <div class="question-answer">
                <p class="w-380">{{$userActionPlanAdvice->measureApplication->measure_name}}</p>
                <p class="w-150">{{\App\Helpers\NumberFormatter::format($userActionPlanAdvice->costs, 0, true)}} {{\App\Helpers\Hoomdossier::getUnitForColumn('costs')}}</p>
                <p class="w-100">{{\App\Services\UserActionPlanAdviceService::getYear($userActionPlanAdvice)}}</p>
            </div>
        @endforeach
    </div>
@endisset
