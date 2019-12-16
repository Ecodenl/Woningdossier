@isset($reportForUser['calculations'][$stepShort][$subStepShort]['insulation_advice'])
    <div class="question-answer-section">
        <div class="question-answer">
            <p class="lead w-380">@lang('pdf/user-report.measure-pages.advice')</p>
            <p>{{$reportForUser['calculations'][$stepShort][$subStepShort]['insulation_advice']}}</p>
        </div>
    </div>
@endisset
