@component('cooperation.pdf.components.new-page')
    <div class="container">

        @include('cooperation.pdf.user-report.parts.measure-page.step-intro')

        <div class="question-answer-section">
            <p class="lead">{{__('pdf/user-report.measure-pages.filled-in-data')}}</p>
            @foreach ($dataForSubStep['building_roof_types'] as $buildingRoofTypeId => $buildingRoofTypeValues)
                <p class="sub-lead">{{\App\Models\RoofType::find($buildingRoofTypeId)->name}}</p>
                <table class="full-width">
                    <tbody>
                    @foreach(\Illuminate\Support\Arr::dot($buildingRoofTypeValues) as $translationKey => $value)
                        <?php
                        $translationForAnswer = $reportTranslations[$stepShort . '.' . $subStepShort . '.building_roof_types.' . $buildingRoofTypeId . '.' . $translationKey];
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
            @endforeach
        </div>

        @include('cooperation.pdf.user-report.parts.measure-page.insulation-advice')

        {{-- Indicative costs and measures  --}}
        <div class="question-answer-section">
            @foreach($calculationsForStep as $roofTypeShort => $calculationResultsForRoofType)
                <p class="lead">{{__('pdf/user-report.roof-insulation.indicative-costs-and-benefits-for-measure.'.$roofTypeShort)}}</p>
                @foreach($calculationResultsForRoofType as $calculationType => $result)
                    <?php
                    $calculationTypesThatCantBeShown = ['type'];
                    $calculationTypeNeedToBeShown = !in_array($calculationType, $calculationTypesThatCantBeShown);
                    $calculationTypeIsYear = \App\Helpers\Hoomdossier::columnContains($calculationType, 'year');
                    ?>
                    @if(!empty($result) && !is_array($result) && $calculationTypeNeedToBeShown)

                        <?php
                        $translationForAnswer = $reportTranslations[$stepShort . '.' . $subStepShort . '.calculation.' . $roofTypeShort . '.' . $calculationType];
                        ?>
                        <table class="full-width">
                            <tbody>
                            <tr class="h-20">
                                <td class="w-380">{{$translationForAnswer}}</td>
                                <td>{{$calculationTypeIsYear ? $calculationResult : \App\Helpers\NumberFormatter::format($calculationResult, 0, true)}} {{\App\Helpers\Hoomdossier::getUnitForColumn($calculationType)}}</td>
                            </tr>
                            </tbody>
                        </table>
                    @endif
                @endforeach
            @endforeach
        </div>
    </div>

@endcomponent

@component('cooperation.pdf.components.new-page')

    <div class="container">
        @include('cooperation.pdf.user-report.parts.measure-page.advices')

        @include('cooperation.pdf.user-report.parts.measure-page.comments')
    </div>
@endcomponent