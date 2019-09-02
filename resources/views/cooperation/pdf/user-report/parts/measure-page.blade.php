@component('cooperation.pdf.components.new-page')
    <div class="container">

        <div class="step-intro">
{{--            <img src="{{public_path('images/icons/'.$step.'.png')}}" alt="">--}}
            <img src="{{asset('images/icons/'.$step.'.png')}}" alt="">
            <h2>{{\App\Models\Step::whereSlug($step)->first()->name}}</h2>
            <p>@lang('pdf/user-report.step-description.'.$step)</p>
        </div>

        <div class="question-answer-section">
            <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.filled-in-data')}}</p>
            <?php
                $calculationsForStep = $data['calculation'] ?? [];
                unset($data['calculation']);
            ?>


            @if($step === 'insulated-glazing')
                <?php
                    // we dont need it, we will use the $buildingInsulatedGlazings
                    unset($data['user_interests'], $data['building_insulated_glazings'])
                ?>
                @foreach($buildingInsulatedGlazings as $buildingInsulatedGlazing)
                    <p class="sub-lead">{{$buildingInsulatedGlazing->measureApplication->measure_name}}</p>
                    <table class="full-width">
                        <tbody>
                            <tr class="h-20">
                                <td class="w-300">{{\App\Helpers\Translation::translate('insulated-glazing.'.$buildingInsulatedGlazing->measureApplication->short.'.current-glass.title')}}</td>
                                <td>{{$buildingInsulatedGlazing->insulatedGlazing->name}}</td>
                            </tr>
                            <tr class="h-20">
                                <td class="w-300">{{\App\Helpers\Translation::translate('insulated-glazing.'.$buildingInsulatedGlazing->measureApplication->short.'.rooms-heated.title')}}</td>
                                <td>{{$buildingInsulatedGlazing->buildingHeating->name}}</td>
                            </tr>
                            <tr class="h-20">
                                <td class="w-300">{{\App\Helpers\Translation::translate('insulated-glazing.'.$buildingInsulatedGlazing->measureApplication->short.'.m2.title')}}</td>
                                <td>{{$buildingInsulatedGlazing->m2}}</td>
                            </tr>
                            <tr class="h-20">
                                <td class="w-300">{{\App\Helpers\Translation::translate('insulated-glazing.'.$buildingInsulatedGlazing->measureApplication->short.'.window-replace.title')}}</td>
                                <td>{{$buildingInsulatedGlazing->windows}}</td>
                            </tr>
                        </tbody>
                    </table>
                @endforeach
            @endif

            <table class="full-width">
                <tbody>
                @foreach (\Illuminate\Support\Arr::dot($data) as $translationKey => $value)
                    <?php
                        $translationForAnswer = $reportTranslations[$step.'.'.$translationKey];
                    ?>
                    <tr class="h-20">
                        <td class="w-300">{{$translationForAnswer}}</td>
                        <td>{{$value}} {{\App\Helpers\Hoomdossier::getUnitForColumn($translationKey)}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>


        <div class="question-answer-section">
            <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.indicative-costs-and-benefits-for-measure')}}</p>

            <table class="full-width">
                <tbody>
                @foreach($calculationsForStep as $calculationType => $calculationResult)
                    @if(!empty($calculationResult) && !is_array($calculationResult))

                    <?php
                        $translationForAnswer = $reportTranslations[$step.'.calculation.'.$calculationType];
                    ?>
                    <tr class="h-20">
                        <td class="w-300">{{$translationForAnswer}}</td>
                        <td>{{(\App\Helpers\NumberFormatter::format($calculationResult, 0, true))}} {{\App\Helpers\Hoomdossier::getUnitForColumn($calculationType)}}</td>
                    </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="question-answer-section">
            <div class="measures">
                <p class="lead w-300">
                    {{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.measures.energy-saving.title')}}
                </p>
                <p class="lead w-150">
                    {{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.measures.energy-saving.costs')}}
                </p>
                <p class="lead w-150">
                    {{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.measures.energy-saving.year')}}
                </p>
            </div>
            @isset($advices['energy_saving'][$step])
                @foreach($advices['energy_saving'][$step] as $userActionPlanAdvice)
                    <div class="question-answer">
                        <p class="w-300">{{$userActionPlanAdvice->measureApplication->measure_name}}</p>
                        <p class="w-150">{{\App\Helpers\NumberFormatter::round($userActionPlanAdvice->costs)}} {{\App\Helpers\Hoomdossier::getUnitForColumn('costs')}}</p>
                        <p class="w-150">{{$userActionPlanAdvice->getYear()}}</p>
                    </div>
                @endforeach
            @endisset
        </div>

        <div class="question-answer-section">
            <div class="measures">
                <p class="lead w-300">
                    {{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.measures.maintenance.title')}}
                </p>
                <p class="lead w-150">
                    {{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.measures.maintenance.costs')}}
                </p>
                <p class="lead w-150">
                    {{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.measures.maintenance.year')}}
                </p>
            </div>

            @isset($advices['maintenance'][$step])
                @foreach($advices['maintenance'][$step] as $userActionPlanAdvice)
                    <div class="question-answer">
                        <p class="w-300">{{$userActionPlanAdvice->measureApplication->measure_name}}</p>
                        <p class="w-150">{{\App\Helpers\NumberFormatter::round($userActionPlanAdvice->costs)}} {{\App\Helpers\Hoomdossier::getUnitForColumn('costs')}}</p>
                        <p class="w-150">{{$userActionPlanAdvice->getYear()}}</p>
                    </div>
                @endforeach
            @endisset
        </div>

        <div class="question-answer-section">
            <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.comments')}}</p>
            @if(array_key_exists($step, $commentsByStep))
                @foreach($commentsByStep[$step] as $inputSourceName => $commentsCategorizedUnderColumn)
                    {{-- The column can be a category, this will be the case when the comment is stored under a catergory --}}
                    @foreach($commentsCategorizedUnderColumn as $columnOrCategory => $comment)
                        <div class="question-answer">
                            @if(is_array($comment))
                                @foreach($comment as $column => $c)
                                    <p class="w-300">{{$inputSourceName}} ({{$columnOrCategory}})</p>
                                    <p>{{$c}}</p>
                                @endforeach
                            @else
                                <p class="w-300">{{$inputSourceName}}</p>
                                <p>{{$comment}}</p>
                            @endif
                        </div>
                    @endforeach
                @endforeach
            @endif
        </div>
    </div>
@endcomponent