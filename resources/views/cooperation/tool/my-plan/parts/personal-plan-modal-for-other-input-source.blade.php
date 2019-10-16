<?php
//filter out the coach comments so we can check if there are any.
$commentsForInputSource = [];
foreach ($commentsByStep as $stepSlug => $commentsByInputSource) {
    // filter the coach comments and leave out empty stuff
    $commentsForInputSource[$stepSlug] = array_filter($commentsByInputSource, function ($inputSource) use ($inputSourceName) {
        return $inputSource === $inputSourceName;
    }, ARRAY_FILTER_USE_KEY);
}
$commentsForInputSource = array_filter($commentsForInputSource);
?>

@component('cooperation.tool.components.modal', ['id' => $inputSourceName, 'class' => 'modal-lg'])
    @slot('title')
        {{$inputSourceName}}
    @endslot
    <h1>@lang('my-plan.personal-plan-modal-for-other-input-source.title', ['input_source_name' => $inputSourceName])</h1>
    <p class="lead">@lang('my-plan.personal-plan-modal-for-other-input-source.text', ['input_source_name' => $inputSourceName])</p>
    @foreach($commentsForInputSource  as $stepSlug => $commentForInputSource)
        <h4>{{\App\Models\Step::where('slug', $stepSlug)->first()->name}}</h4>
        @foreach($commentForInputSource as $comment)
            <p>{{$comment}}</p>
        @endforeach
        <hr>
    @endforeach


    @foreach($measuresByYear as $year => $stepMeasures)
        <li style="list-style: none;">
            <h1>{{$year}}</h1>
            <table class="table table-condensed table-responsive">
                <thead>
                <tr>
                    <th style="width: 8%">{{ \App\Helpers\Translation::translate('my-plan.columns.more-info.title') }}</th>
                    <th style="width: 45%">{{ \App\Helpers\Translation::translate('my-plan.columns.measure.title') }}</th>
                    <th style="width: 12%">{{ \App\Helpers\Translation::translate('my-plan.columns.costs.title') }}</th>
                    <th style="width: 12%">{{ \App\Helpers\Translation::translate('my-plan.columns.savings-costs.title') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($stepMeasures as $stepName => $measures)
                    @foreach($measures as $measure)
                        <tr>
                            <td>
                                <a type="#" data-toggle="collapse" data-target="#more-info-{{$measure['advice_id']}}-{{$inputSourceName}}"> <i class="glyphicon glyphicon-chevron-down"></i> </a>
                            </td>
                            <td>
                                {{ $measure['measure'] }}
                            </td>
                            <td>
                                &euro; {{ \App\Helpers\NumberFormatter::format($measure['costs'], 0, true) }}
                            </td>
                            <td>
                                &euro; {{ \App\Helpers\NumberFormatter::format($measure['savings_money'], 0, true) }}
                            </td>
                        </tr>
                        <tr class="collapse" id="more-info-{{$measure['advice_id']}}-{{$inputSourceName}}">
                            <td colspan="1">
                            </td>
                            <td>
                                <strong>{{ \App\Helpers\Translation::translate('my-plan.columns.savings-gas.title') }}:</strong>
                                <br>
                                <strong>{{ \App\Helpers\Translation::translate('my-plan.columns.savings-electricity.title') }}:</strong>
                            </td>
                            <td>
                                {{ \App\Helpers\NumberFormatter::format($measure['savings_gas'], 0, true) }} m<sup>3</sup>
                                <br>
                                {{ \App\Helpers\NumberFormatter::format($measure['savings_electricity'], 0, true) }} kWh
                            </td>
                            <td colspan="3">
                            </td>
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </li>
    @endforeach
@endcomponent