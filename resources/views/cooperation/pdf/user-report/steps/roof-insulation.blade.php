@component('cooperation.pdf.components.new-page')
    <div class="container">

        @include('cooperation.pdf.user-report.parts.step-intro')

        <div class="question-answer-section">
            <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.filled-in-data')}}</p>
            @foreach ($dataForStep['building_roof_types'] as $buildingRoofTypeId => $buildingRoofTypeValues)
                <p class="sub-lead">{{\App\Models\RoofType::find($buildingRoofTypeId)->name}}</p>
                <table class="full-width">
                    <tbody>
                    @foreach(\Illuminate\Support\Arr::dot($buildingRoofTypeValues) as $translationKey => $value)
                        <?php
                        $translationForAnswer = $reportTranslations[$stepSlug.'.building_roof_types.'.$buildingRoofTypeId.'.'.$translationKey];
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

        @include('cooperation.pdf.user-report.parts.insulation-advice')

        {{-- Indicative costs and measures  --}}
        <div class="question-answer-section">
            @foreach($calculationsForStep as $roofTypeShort => $calculationResultsForRoofType)
                <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.roof-insulation.indicative-costs-and-benefits-for-measure.'.$roofTypeShort)}}</p>
                @foreach($calculationResultsForRoofType as $calculationType => $result)
                    @if(!empty($result) && !is_array($result))

                        <?php
                            $translationForAnswer = $reportTranslations[$stepSlug . '.calculation.'.$roofTypeShort.'.' . $calculationType];
                        ?>
                        <table class="full-width">
                            <tbody>
                            <tr class="h-20">
                                <td class="w-380">{{$translationForAnswer}}</td>
                                <td>{{!\App\Helpers\Hoomdossier::columnContains($calculationType, 'year') ? \App\Helpers\NumberFormatter::format($result, 0, true) : $result}} {{\App\Helpers\Hoomdossier::getUnitForColumn($calculationType)}}</td>
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
        @include('cooperation.pdf.user-report.parts.advices')

        @include('cooperation.pdf.user-report.parts.comments')
    </div>
@endcomponent