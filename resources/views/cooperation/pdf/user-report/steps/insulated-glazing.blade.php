@component('cooperation.pdf.components.new-page')
    <div class="container">

        @include('cooperation.pdf.user-report.parts.measure-page.step-intro')

        <div class="question-answer-section">
            <p class="lead">{{__('pdf/user-report.measure-pages.filled-in-data')}}</p>

            <?php
                // we dont need it, we will use the $buildingInsulatedGlazings
                // we also MUST unset them otherwise they will be picked up later on and we will get duplicates
                unset($dataForSubStep['considerables'], $dataForSubStep['building_insulated_glazings']);

                $insulatedGlazingStep = App\Models\Step::findByShort($stepShort);

                // get all the advices for the insulated glazings step
                // then pluck the measure application ids, that way we can only show those toes
                $measureApplicationIds = $userActionPlanAdvices
                    ->where('step_id', $insulatedGlazingStep->id)
                    ->pluck('user_action_plan_advisable_id')
                    ->toArray();

                // we will only show the building insulated glazings which are shown on the woonplan
                $buildingInsulatedGlazings = $buildingInsulatedGlazings->whereIn('measure_application_id', $measureApplicationIds)
            ?>
            @foreach($buildingInsulatedGlazings as $buildingInsulatedGlazing)

                <p class="sub-lead">{{$buildingInsulatedGlazing->measureApplication->measure_name}}</p>
                <table class="full-width">
                    <tbody>
                    <tr class="h-20">
                        <td class="w-380">{{__('default.considering', ['name' => $buildingInsulatedGlazing->measureApplication->measure_name])}}</td>
                        <td>{{$user->considers($buildingInsulatedGlazing->measureApplication, $inputSource) ? __('default.yes') : __('default.no') }}</td>
                    </tr>
                    <tr class="h-20">
                        <td class="w-380">{{__('insulated-glazing.'.$buildingInsulatedGlazing->measureApplication->short.'.rooms-heated.title')}}</td>
                        <td>{{optional($buildingInsulatedGlazing->buildingHeating)->name}}</td>
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