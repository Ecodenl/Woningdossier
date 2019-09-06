<?php
// the insulated glazing need a different layout / structure then the $data gives us.
// its easier, faster and more readable to do it in this way then do magic on all the array keys.
// however, we should avoid this as much a possible otherwise the code will be bloated

    // calculations
    $calculationsForStep = $dataForStep['calculation'] ?? [];
    unset($dataForStep['calculation']);
?>
@if($stepSlug === 'insulated-glazing')

    @component('cooperation.pdf.components.new-page')
        <div class="container">

            @include('cooperation.pdf.user-report.parts.step-intro')

            <div class="question-answer-section">
                <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.filled-in-data')}}</p>

                    <?php
                        // we dont need it, we will use the $buildingInsulatedGlazings
                        // we also MUST unset them otherwise they will be picked up later on and we will get duplicates
                        unset($dataForStep['user_interests'], $dataForStep['building_insulated_glazings'])
                    ?>
                    @foreach($buildingInsulatedGlazings as $buildingInsulatedGlazing)

                        <?php

                        ?>
                        <p class="sub-lead">{{$buildingInsulatedGlazing->measureApplication->measure_name}}</p>
                        <table class="full-width">
                            <tbody>
                            <tr class="h-20">
                                <td class="w-380">{{\App\Helpers\Translation::translate('insulated-glazing.'.$buildingInsulatedGlazing->measureApplication->short.'.title.title')}}</td>
                                <td>{{$user->getInterestedType('measure_application', $buildingInsulatedGlazing->measureApplication->id, $inputSource)->interest->name}}</td>
                            </tr>
                            <tr class="h-20">
                                <td class="w-380">{{\App\Helpers\Translation::translate('insulated-glazing.'.$buildingInsulatedGlazing->measureApplication->short.'.current-glass.title')}}</td>
                                <td>{{$buildingInsulatedGlazing->insulatedGlazing->name}}</td>
                            </tr>
                            <tr class="h-20">
                                <td class="w-380">{{\App\Helpers\Translation::translate('insulated-glazing.'.$buildingInsulatedGlazing->measureApplication->short.'.current-glass.title')}}</td>
                                <td>{{$buildingInsulatedGlazing->insulatedGlazing->name}}</td>
                            </tr>
                            <tr class="h-20">
                                <td class="w-380">{{\App\Helpers\Translation::translate('insulated-glazing.'.$buildingInsulatedGlazing->measureApplication->short.'.current-glass.title')}}</td>
                                <td>{{$buildingInsulatedGlazing->insulatedGlazing->name}}</td>
                            </tr>
                            <tr class="h-20">
                                <td class="w-380">{{\App\Helpers\Translation::translate('insulated-glazing.'.$buildingInsulatedGlazing->measureApplication->short.'.rooms-heated.title')}}</td>
                                <td>{{$buildingInsulatedGlazing->buildingHeating->name}}</td>
                            </tr>
                            <tr class="h-20">
                                <td class="w-380">{{\App\Helpers\Translation::translate('insulated-glazing.'.$buildingInsulatedGlazing->measureApplication->short.'.m2.title')}}</td>
                                <td>{{$buildingInsulatedGlazing->m2}} m2</td>
                            </tr>
                            <tr class="h-20">
                                <td class="w-380">{{\App\Helpers\Translation::translate('insulated-glazing.'.$buildingInsulatedGlazing->measureApplication->short.'.window-replace.title')}}</td>
                                <td>{{$buildingInsulatedGlazing->windows}}</td>
                            </tr>
                            </tbody>
                        </table>
                    @endforeach
            </div>
        </div>
    @endcomponent

    @component('cooperation.pdf.components.new-page')
        <div class="container">

            <br>

            <div class="question-answer-section">
                <p class="lead">{{\App\Helpers\Translation::translate('insulated-glazing.paint-work.title.title')}}</p>
                <table class="full-width">
                    <tbody>
                    @foreach (\Illuminate\Support\Arr::dot($dataForStep) as $translationKey => $value)
                        <?php
                            $translationForAnswer = $reportTranslations[$stepSlug . '.' . $translationKey];
                        ?>
                        <tr class="h-20">
                            <td class="w-380">{{$translationForAnswer}}</td>
                            <td>{{$value}} {{\App\Helpers\Hoomdossier::getUnitForColumn($translationKey)}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            @include('cooperation.pdf.user-report.parts.insulation-advice')

            @include('cooperation.pdf.user-report.parts.indicative-costs-and-measures')

            @include('cooperation.pdf.user-report.parts.advices')

            @include('cooperation.pdf.user-report.parts.comments')
        </div>
    @endcomponent
@else
    @component('cooperation.pdf.components.new-page')
        <div class="container">

            @include('cooperation.pdf.user-report.parts.step-intro')

            @include('cooperation.pdf.user-report.parts.filled-in-data')

            @include('cooperation.pdf.user-report.parts.insulation-advice')

            @include('cooperation.pdf.user-report.parts.indicative-costs-and-measures')

            @include('cooperation.pdf.user-report.parts.advices')

            @include('cooperation.pdf.user-report.parts.comments')
        </div>
    @endcomponent
@endif
