@isset($measures['maintenance'][$stepShort])
    <table class="full-width">
        <thead class="no-background lead">
            <tr>
                <th>@lang('pdf/user-report.measure-pages.measures.maintenance.title')</th>
                <th>@lang('pdf/user-report.measure-pages.measures.maintenance.costs')</th>
                <th>@lang('pdf/user-report.measure-pages.measures.maintenance.year')</th>
            </tr>
        </thead>
        <tbody>
        @foreach($measures['maintenance'][$stepShort] as $userActionPlanAdvice)
            <tr class="h-20">
                <td class="w-380">{{$userActionPlanAdvice->measureApplication->measure_name}}</td>
                <td class="w-150">{{\App\Helpers\NumberFormatter::format($userActionPlanAdvice->costs, 0, true)}} {{\App\Helpers\Hoomdossier::getUnitForColumn('costs')}}</td>
                <td class="w-100">{{\App\Services\UserActionPlanAdviceService::getYear($userActionPlanAdvice)}}</td>
            </tr>
        @endforeach

        </tbody>
    </table>

@endisset
