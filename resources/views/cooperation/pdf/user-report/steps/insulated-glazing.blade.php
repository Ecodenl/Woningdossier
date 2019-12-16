@component('cooperation.pdf.components.new-page')
    <div class="container">

        @include('cooperation.pdf.user-report.parts.measure-page.step-intro')

        <div class="question-answer-section">
            <p class="lead">{{__('pdf/user-report.measure-pages.filled-in-data')}}</p>

            <?php
                // we dont need it, we will use the $buildingInsulatedGlazings
                // we also MUST unset them otherwise they will be picked up later on and we will get duplicates
                unset($dataForSubStep['user_interests'], $dataForSubStep['building_insulated_glazings']);
            ?>
            @foreach($buildingInsulatedGlazings as $buildingInsulatedGlazing)

                <?php

                ?>
                <p class="sub-lead">{{$buildingInsulatedGlazing->measureApplication->measure_name}}</p>
                <table class="full-width">
                    <tbody>
                    <tr class="h-20">
                        <td class="w-380">{{__('insulated-glazing.'.$buildingInsulatedGlazing->measureApplication->short.'.title.title')}}</td>
                        {{--<td>{{$user->getInterestedType('measure_application', $buildingInsulatedGlazing->measureApplication->id, $inputSource)->interest->name ?? $noInterest->name}}</td>--}}
                        <td>{{$user->userInterestsForSpecificType(get_class($buildingInsulatedGlazing->measureApplication), $buildingInsulatedGlazing->measureApplication->id, $inputSource)->first()->interest->name ?? $noInterest->name}}</td>
                    </tr>
                    <tr class="h-20">
                        <td class="w-380">{{__('insulated-glazing.'.$buildingInsulatedGlazing->measureApplication->short.'.rooms-heated.title')}}</td>
                        <td>{{$buildingInsulatedGlazing->buildingHeating->name}}</td>
                    </tr>
                    <tr class="h-20">
                        <td class="w-380">{{__('insulated-glazing.'.$buildingInsulatedGlazing->measureApplication->short.'.m2.title')}}</td>
                        <td>{{$buildingInsulatedGlazing->m2}} m2</td>
                    </tr>
                    <tr class="h-20">
                        <td class="w-380">{{__('insulated-glazing.'.$buildingInsulatedGlazing->measureApplication->short.'.window-replace.title')}}</td>
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

        @include('cooperation.pdf.user-report.parts.measure-page.filled-in-data', [
            'dataForSubStep' => \Illuminate\Support\Arr::dot($dataForSubStep),
            'title' => __('insulated-glazing.paint-work.title.title')
        ])

        @include('cooperation.pdf.user-report.parts.measure-page.insulation-advice')

        @include('cooperation.pdf.user-report.parts.measure-page.indicative-costs-and-measures')

        @include('cooperation.pdf.user-report.parts.measure-page.advices')


        @if(isset($commentsByStep[$stepShort][$subStepShort]) && !\App\Helpers\Arr::isWholeArrayEmpty($commentsByStep[$stepShort][$subStepShort]))
            @include('cooperation.pdf.user-report.parts.measure-page.comments', [
                'comments' => $commentsByStep[$stepShort][$subStepShort],
            ])
        @endif
    </div>
@endcomponent