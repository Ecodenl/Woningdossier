<div class="question-answer-section">
    <p class="lead">{{__('pdf/user-report.measure-pages.filled-in-data')}}</p>
    <table class="full-width">
        <tbody>
        @foreach (\Illuminate\Support\Arr::dot($dataForSubStep) as $translationKey => $value)
            <?php
            $translationForAnswer = $reportTranslations[$stepShort . '.' . $subStepShort . '.' . $translationKey];

            $tableIsNotUserInterest = !\App\Helpers\Hoomdossier::columnContains($translationKey, 'user_interests');
            ?>
            {{--we only want the interest on the interest page--}}
            @if($tableIsNotUserInterest || $subStepShort == 'interest')
                <tr class="h-20">
                    <td class="w-380">{{$translationForAnswer}}</td>
                    <td>{{$value}} {{\App\Helpers\Hoomdossier::getUnitForColumn($translationKey)}}</td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
</div>