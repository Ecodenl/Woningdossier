<div class="question-answer-section">
    <h2>{{__('pdf/user-report.general-data.attachment.title')}}</h2>
    <p class="lead">{{__('pdf/user-report.general-data.attachment.lead')}}</p>
    <ol>
        @foreach($steps as $step)
            <li>{{$step->name}}</li>
        @endforeach
    </ol>
    <p>{!!\App\Helpers\Translation::translate('pdf/user-report.general-data.attachment.text')!!}</p>
</div>