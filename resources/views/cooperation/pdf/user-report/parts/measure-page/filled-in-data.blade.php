<div class="question-answer-section">
    <p class="lead">{{$title ?? __('pdf/user-report.measure-pages.filled-in-data')}}</p>
    <table class="full-width">
        <tbody>
        @foreach ($dataForSubStep as $translationKey => $value)
            <?php
                $translationForAnswer = $reportTranslations[$stepShort . '.' . $subStepShort . '.' . $translationKey];


                $tableIsNotUserInterest = !\App\Helpers\Hoomdossier::columnContains($translationKey, 'considerables');
                $isNotCalculation = !\App\Helpers\Hoomdossier::columnContains($translationKey, 'calculation');

                $doesAnswerContainUnit = stripos($value, 'm2') !== false;
            ?>
            {{--we only want the interest on the interest page--}}
            @if($isNotCalculation)
                <tr class="h-20">
                    <td class="w-380">{{$translationForAnswer}}</td>
                    <td>{{$value}} {{$doesAnswerContainUnit ?'': \App\Helpers\Hoomdossier::getUnitForColumn($translationKey)}}</td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
</div>