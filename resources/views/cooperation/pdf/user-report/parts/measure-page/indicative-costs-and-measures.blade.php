<div class="question-answer-section">
    <p class="lead">{{__('pdf/user-report.measure-pages.indicative-costs-and-benefits-for-measure')}}</p>

    <table class="full-width">
        <tbody>
        @if($stepShort == 'solar-panels')
            <?php
                $calculationsForStep = array_except($calculationsForStep, [
                    'year', 'advice', 'total_power', 'performance'
                ]);
            ?>
        @endif
        @foreach($calculationsForStep as $calculationType => $calculationResult)
            @if(!empty($calculationResult) && !is_array($calculationResult))
                {{--the advice will be shown in a different form--}}
                @continue(\App\Helpers\Hoomdossier::columnContains($calculationType, 'advice'))

                <?php
                    $calculationTypeIsYear = \App\Helpers\Hoomdossier::columnContains($calculationType, 'year');
                    $translationForAnswer = $reportTranslations[$stepShort . '.' . $subStepShort . '.calculation.' . $calculationType];
                    $formattedCalculationResult = $calculationTypeIsYear ? $calculationResult : \App\Helpers\NumberFormatter::format($calculationResult, 0, true)
                ?>
                <tr class="h-20">
                    <td class="w-380">{{$translationForAnswer}}</td>
                    <td>{{$calculationResult}} {{\App\Helpers\Hoomdossier::getUnitForColumn($calculationType)}}</td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
</div>
