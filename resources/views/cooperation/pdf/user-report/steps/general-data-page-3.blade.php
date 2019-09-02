<div class="question-answer-section">
    <h2>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.attachment.title')}}</h2>
    <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.attachment.lead')}}</p>
    <ol>
        @foreach($steps as $step)
            @if(\App\Helpers\StepHelper::hasInterestInStep($building, $step))
            <li>{{$step->name}}</li>
            @endif
        @endforeach
    </ol>
    <p>{!!\App\Helpers\Translation::translate('pdf/user-report.general-data.attachment.text')!!}</p>
</div>