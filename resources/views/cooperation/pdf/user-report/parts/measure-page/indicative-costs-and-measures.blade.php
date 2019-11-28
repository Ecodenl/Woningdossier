<div class="question-answer-section">
    <p class="lead">{{__('pdf/user-report.measure-pages.indicative-costs-and-benefits-for-measure')}}</p>

    <table class="full-width">
        <tbody>
        @foreach($calculationsForStep as $calculationType => $calculationResult)
            <?php
            $calculationTypesThatCantBeShown = ['insulation_advice'];
            $calculationTypeNeedToBeShown = !in_array($calculationType, $calculationTypesThatCantBeShown);
            $calculationTypeIsYear = \App\Helpers\Hoomdossier::columnContains($calculationType, 'year');
            ?>

            {{--the advice will be shown in a different form--}}
            @if(!empty($calculationResult) && !is_array($calculationResult) && $calculationTypeNeedToBeShown)
                <?php
                $translationForAnswer = $reportTranslations[$stepShort . '.' . $subStepShort . '.calculation.' . $calculationType];
                ?>
                <tr class="h-20">
                    <td class="w-380">{{$translationForAnswer}}</td>
                    <td>{{$calculationTypeIsYear ? $calculationResult : \App\Helpers\NumberFormatter::format($calculationResult, 0, true)}} {{\App\Helpers\Hoomdossier::getUnitForColumn($calculationType)}}</td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
</div>
