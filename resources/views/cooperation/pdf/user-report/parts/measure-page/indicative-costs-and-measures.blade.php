<div class="question-answer-section">
    <p class="lead">{{__('pdf/user-report.measure-pages.indicative-costs-and-benefits-for-measure')}}</p>

    <table class="full-width">
            <tbody>
        @foreach($calculationsForStep as $calculationType => $calculationResult)
            @if(!empty($calculationResult) && !is_array($calculationResult))
                <?php
                    $translationForAnswer = $reportTranslations[$stepShort . '.' . $subStepShort. '.calculation.' . $calculationType];
                ?>
                <tr class="h-20">
                    <td class="w-380">{{$translationForAnswer}}</td>
                    <td>{{!\App\Helpers\Hoomdossier::columnContains($calculationType, 'year') ? \App\Helpers\NumberFormatter::format($calculationResult, 0, true) : $calculationResult}} {{\App\Helpers\Hoomdossier::getUnitForColumn($calculationType)}}</td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
</div>
