<div class="question-answer-section">
    <p class="lead">{{__('pdf/user-report.measure-pages.filled-in-data')}}</p>
    <table class="full-width">
        <tbody>
        @foreach (\Illuminate\Support\Arr::dot($dataForSubStep) as $translationKey => $value)
            <?php
            $translationForAnswer = $reportTranslations[$stepShort . '.' . $subStepShort . '.' . $translationKey];
            ?>
            @if(!\App\Helpers\Hoomdossier::columnContains($translationKey, 'user_interest'))
                <tr class="h-20">
                    <td class="w-380">{{$translationForAnswer}}</td>
                    <td>{{$value}} {{\App\Helpers\Hoomdossier::getUnitForColumn($translationKey)}}</td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
</div>