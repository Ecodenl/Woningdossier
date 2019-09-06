@isset($reportForUser['calculations'][$stepSlug]['insulation_advice'])
    <div class="question-answer-section">
        <div class="question-answer">
            <p class="lead w-320">@lang('pdf/user-report.measure-pages.advice')</p>
            <p>{{$reportForUser['calculations'][$stepSlug]['insulation_advice']}}</p>
        </div>
    </div>
@endisset
