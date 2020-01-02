@isset($reportForUser['calculations'][$stepShort][$subStepShort]['insulation_advice'])
    <div class="question-answer-section">

        <table class="full-width">
            <tbody>
            <tr class="h-20">
                <td class="w-380">@lang('pdf/user-report.measure-pages.advice')</td>
                <td>{{$reportForUser['calculations'][$stepShort][$subStepShort]['insulation_advice']}}</td>
            </tr>
            </tbody>
        </table>
    </div>
@endisset
