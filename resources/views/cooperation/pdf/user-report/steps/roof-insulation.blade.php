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

        <?php
            // this will check if ALL calcs are empty, we still have to check the specific by typ.
            // ex: flat can be empty but pitched wont
            $calculationsAreEmpty = \App\Helpers\Arr::isWholeArrayEmpty($calculationsForStep);
            $nonEmptyRoofInsulationCalculations = [];
        ?>
        @if(!$calculationsAreEmpty)
            {{-- Indicative costs and measures  --}}
            <div class="question-answer-section">
                @foreach($calculationsForStep as $roofTypeShort => $calculationResultsForRoofType)
                    <?php
                        // we wont show that here
                        unset($calculationResultsForRoofType['replace']);
                    ?>
                    @if(!\App\Helpers\Arr::isWholeArrayEmpty($calculationResultsForRoofType))
                        <?php $nonEmptyRoofInsulationCalculations[] = $roofTypeShort ?>
                        <p class="lead">{{__('pdf/user-report.roof-insulation.indicative-costs-and-benefits-for-measure.'.$roofTypeShort)}}</p>
                        <table class="full-width">
                            <tbody>
                            @foreach($calculationResultsForRoofType as $calculationType => $result)
                                <?php
                                $calculationTypesThatCantBeShown = ['type'];
                                $calculationTypeNeedToBeShown = !in_array($calculationType, $calculationTypesThatCantBeShown);
                                $calculationTypeIsYear = \App\Helpers\Hoomdossier::columnContains($calculationType, 'year');
                                ?>
                                @if(!empty($result) && $calculationTypeNeedToBeShown)

                                    <?php
                                    $translationForAnswer = $reportTranslations[$stepShort . '.' . $subStepShort . '.calculation.' . $roofTypeShort . '.' . $calculationType];
                                    ?>

                                    <tr class="h-20">
                                        <td class="w-380">{{$translationForAnswer}}</td>
                                        <td>{{$calculationTypeIsYear ? $result : \App\Helpers\NumberFormatter::format($result, 0, true)}} {{\App\Helpers\Hoomdossier::getUnitForColumn($calculationType)}}</td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                @endforeach
            </div>
        @endif

        {{-- When there is only 1 calculation  result we can show this, and otherwise its pech hebben. --}}
        @if(count($calculationsForStep) <= 1 || count($nonEmptyRoofInsulationCalculations) < 2)
            @include('cooperation.pdf.user-report.parts.measure-page.advices')

            @include('cooperation.pdf.user-report.parts.measure-page.comments')
        @endif
    </div>

@endcomponent

{{-- When there are multiple calcualtion results there wont be enough space on 1 page to display all the info --}}
@if(count($calculationsForStep) > 1 || count($nonEmptyRoofInsulationCalculations) > 1)
    @component('cooperation.pdf.components.new-page')

        <div class="container">
            @include('cooperation.pdf.user-report.parts.measure-page.advices')

            @include('cooperation.pdf.user-report.parts.measure-page.comments')
        </div>
    @endcomponent
@endif