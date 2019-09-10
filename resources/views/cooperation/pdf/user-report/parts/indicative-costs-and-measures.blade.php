<div class="question-answer-section">
    <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.indicative-costs-and-benefits-for-measure')}}</p>

    <table class="full-width">
        <tbody>
        @foreach($calculationsForStep as $calculationType => $calculationResult)
            @if(!empty($calculationResult) && !is_array($calculationResult))

                <?php
                $translationForAnswer = $reportTranslations[$stepSlug . '.calculation.' . $calculationType];
                ?>
                <tr class="h-20">
                    <td class="w-380">{{$translationForAnswer}}</td>
                    <td>{{(\App\Helpers\NumberFormatter::format($calculationResult, 0, true))}} {{\App\Helpers\Hoomdossier::getUnitForColumn($calculationType)}}</td>
                </tr>

            @elseif($stepSlug == 'roof-insulation')
                <?php $roofType = $calculationType; ?>
                @foreach($calculationResult as $calculationType => $result)
                    @if(!empty($result) && !is_array($result))
                        <?php
                            $translationForAnswer = $reportTranslations[$stepSlug . '.calculation.'.$roofType.'.' . $calculationType];
                        ?>
                        <tr class="h-20">
                            <td class="w-380">{{$translationForAnswer}}</td>
                            <td>{{(\App\Helpers\NumberFormatter::format($result, 0, true))}} {{\App\Helpers\Hoomdossier::getUnitForColumn($calculationType)}}</td>
                        </tr>
                    @endif
                @endforeach
            @endif
        @endforeach
        </tbody>
    </table>
</div>
