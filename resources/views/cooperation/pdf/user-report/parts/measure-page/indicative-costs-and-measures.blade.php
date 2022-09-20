@if(!\App\Helpers\Arr::isWholeArrayEmpty($calculationsForStep))
    <div class="question-answer-section">
        <p class="lead">{{__('pdf/user-report.measure-pages.indicative-costs-and-benefits-for-measure')}}</p>

        <table class="full-width">
            <tbody>
            @if(in_array($stepShort, ['heater', 'solar-panels']))
                <?php
                // remove some results that are irrelevant to this section
                $calculationsForStep = Arr::except($calculationsForStep, [
                    'year', 'total_power',
                ]);
                ?>
            @endif

            @foreach($calculationsForStep as $calculationType => $calculationResult)
                @if(! empty($calculationResult) && ! is_array($calculationResult))
                    @php $transKey = $stepShort . '.' . $subStepShort . '.calculation.' . $calculationType; @endphp

                    {{--the advice will be shown in a different form--}}
                    @continue(Str::contains($calculationType, 'advice')
                        || Str::contains($calculationType, 'crawlspace_access')
                        || Str::contains($calculationType, 'crawlspace')
                        || ! array_key_exists($transKey, $reportTranslations)
                    )

                    @php
                        $calculationTypeIsYear = \App\Helpers\Hoomdossier::columnContains($calculationType, 'year');
                        $translationForAnswer = $reportTranslations[$transKey];
                        $formattedCalculationResult = $calculationTypeIsYear ? $calculationResult : \App\Helpers\NumberFormatter::format($calculationResult, 0, true)
                    @endphp
                    <tr class="h-20">
                        <td class="w-380">{{$translationForAnswer}}</td>
                        <td>{{$formattedCalculationResult}} {{\App\Helpers\Hoomdossier::getUnitForColumn($calculationType)}}</td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>
@endif
