{{--@isset($advices['energy_saving'][$stepShort])--}}
    {{--<div class="question-answer-section">--}}
        {{--<div class="measures">--}}
            {{--<p class="lead w-380">--}}
                {{--{{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.measures.energy-saving.title')}}--}}
            {{--</p>--}}
            {{--<p class="lead w-150">--}}
                {{--{{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.measures.energy-saving.costs')}}--}}
            {{--</p>--}}
            {{--<p class="lead w-150">--}}
                {{--{{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.measures.energy-saving.year')}}--}}
            {{--</p>--}}
        {{--</div>--}}
        {{--@foreach($advices['energy_saving'][$stepShort] as $userActionPlanAdvice)--}}
            {{--<div class="question-answer">--}}
                {{--<p class="w-380">{{$userActionPlanAdvice->measureApplication->measure_name}}</p>--}}
                {{--<p class="w-150">{{\App\Helpers\NumberFormatter::format($userActionPlanAdvice->costs, 0, true)}} {{\App\Helpers\Hoomdossier::getUnitForColumn('costs')}}</p>--}}
                {{--<p class="w-150">{{$userActionPlanAdvice->getYear()}}</p>--}}
            {{--</div>--}}
        {{--@endforeach--}}
    {{--</div>--}}
{{--@endisset--}}


@isset($advices['maintenance'][$stepShort][$subStepShort])
    <div class="question-answer-section">
        <div class="measures">
            <p class="lead w-380">
                {{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.measures.maintenance.title')}}
            </p>
            <p class="lead w-150">
                {{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.measures.maintenance.costs')}}
            </p>
            <p class="lead w-150">
                {{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.measures.maintenance.year')}}
            </p>
        </div>
        @foreach($advices['maintenance'][$stepShort] as $userActionPlanAdvice)
            <div class="question-answer">
                <p class="w-380">{{$userActionPlanAdvice->measureApplication->measure_name}}</p>
                <p class="w-150">{{\App\Helpers\NumberFormatter::format($userActionPlanAdvice->costs, 0, true)}} {{\App\Helpers\Hoomdossier::getUnitForColumn('costs')}}</p>
                <p class="w-100">{{$userActionPlanAdvice->getYear($inputSource)}}</p>
            </div>
        @endforeach
    </div>
@endisset
