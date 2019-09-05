@component('cooperation.pdf.components.new-page')
    <div class="container">

        <div class="step-intro">
            {{--            <img src="{{public_path('images/icons/'.$stepSlug.'.png')}}" alt="">--}}
            <img src="{{asset('images/icons/'.$stepSlug.'.png')}}" alt="">
            <h2>{{\App\Models\Step::whereSlug($stepSlug)->first()->name}}</h2>
            <p>@lang('pdf/user-report.step-description.'.$stepSlug)</p>
        </div>

        <div class="question-answer-section">
            <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.filled-in-data')}}</p>
            <?php
                $calculationsForStep = $data['calculation'] ?? [];
                unset($data['calculation']);
            ?>


            @if($stepSlug === 'insulated-glazing')
                <?php
                // the insulated glazing need a different layout / structure then the $data gives us.
                // its easier, faster and more readable to do it in this way then do magic on all the array keys.
                // however, we should avoid this as much a possible otherwise the code will be bloated

                // we dont need it, we will use the $buildingInsulatedGlazings
                unset($data['user_interests'], $data['building_insulated_glazings'])
                ?>
                @foreach($buildingInsulatedGlazings as $buildingInsulatedGlazing)

                    <?php

                    ?>
                    <p class="sub-lead">{{$buildingInsulatedGlazing->measureApplication->measure_name}}</p>
                    <table class="full-width">
                        <tbody>
                        <tr class="h-20">
                            <td align="top" class="w-320">{{\App\Helpers\Translation::translate('insulated-glazing.'.$buildingInsulatedGlazing->measureApplication->short.'.title.title')}}</td>
                            <td align="top">{{$user->getInterestedType('measure_application', $buildingInsulatedGlazing->measureApplication->id)->interest->name}}</td>
                        </tr>
                        <tr class="h-20">
                            <td class="w-320">{{\App\Helpers\Translation::translate('insulated-glazing.'.$buildingInsulatedGlazing->measureApplication->short.'.current-glass.title')}}</td>
                            <td>{{$buildingInsulatedGlazing->insulatedGlazing->name}}</td>
                        </tr>
                        <tr class="h-20">
                            <td class="w-320">{{\App\Helpers\Translation::translate('insulated-glazing.'.$buildingInsulatedGlazing->measureApplication->short.'.current-glass.title')}}</td>
                            <td>{{$buildingInsulatedGlazing->insulatedGlazing->name}}</td>
                        </tr>
                        <tr class="h-20">
                            <td class="w-320">{{\App\Helpers\Translation::translate('insulated-glazing.'.$buildingInsulatedGlazing->measureApplication->short.'.current-glass.title')}}</td>
                            <td>{{$buildingInsulatedGlazing->insulatedGlazing->name}}</td>
                        </tr>
                        <tr class="h-20">
                            <td class="w-320">{{\App\Helpers\Translation::translate('insulated-glazing.'.$buildingInsulatedGlazing->measureApplication->short.'.rooms-heated.title')}}</td>
                            <td>{{$buildingInsulatedGlazing->buildingHeating->name}}</td>
                        </tr>
                        <tr class="h-20">
                            <td class="w-320">{{\App\Helpers\Translation::translate('insulated-glazing.'.$buildingInsulatedGlazing->measureApplication->short.'.m2.title')}}</td>
                            <td>{{$buildingInsulatedGlazing->m2}} m2</td>
                        </tr>
                        <tr class="h-20">
                            <td class="w-320">{{\App\Helpers\Translation::translate('insulated-glazing.'.$buildingInsulatedGlazing->measureApplication->short.'.window-replace.title')}}</td>
                            <td>{{$buildingInsulatedGlazing->windows}}</td>
                        </tr>
                        </tbody>
                    </table>
                @endforeach
                <br>
            @endif

            @if($stepSlug === 'insulated-glazing')
                <p class="lead">Vragen over schilderwerk</p>
            @endif
            <table class="full-width">
                <tbody>
                @foreach (\Illuminate\Support\Arr::dot($data) as $translationKey => $value)
                    <?php
                        $translationForAnswer = $reportTranslations[$stepSlug . '.' . $translationKey];
                    ?>
                    @if(!\App\Helpers\Hoomdossier::columnContains($translationKey, 'user_interest'))
                        <tr class="h-20">
                            <td class="w-320">{{$translationForAnswer}}</td>
                            <td>{{$value}} {{\App\Helpers\Hoomdossier::getUnitForColumn($translationKey)}}</td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>

        @if(isset($reportForUser['calculations'][$stepSlug]['insulation_advice']))
            <div class="question-answer-section">
                <div class="question-answer">
                    <p class="lead w-320">@lang('pdf/user-report.measure-pages.advice')</p>
                    <p>{{$reportForUser['calculations'][$stepSlug]['insulation_advice']}}</p>
                </div>
            </div>
        @endisset

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
                            <td class="w-320">{{$translationForAnswer}}</td>
                            <td>{{(\App\Helpers\NumberFormatter::format($calculationResult, 0, true))}} {{\App\Helpers\Hoomdossier::getUnitForColumn($calculationType)}}</td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>

        @isset($advices['energy_saving'][$stepSlug])
            <div class="question-answer-section">
                <div class="measures">
                    <p class="lead w-320">
                        {{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.measures.energy-saving.title')}}
                    </p>
                    <p class="lead w-150">
                        {{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.measures.energy-saving.costs')}}
                    </p>
                    <p class="lead w-150">
                        {{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.measures.energy-saving.year')}}
                    </p>
                </div>
                @foreach($advices['energy_saving'][$stepSlug] as $userActionPlanAdvice)
                    <div class="question-answer">
                        <p class="w-320">{{$userActionPlanAdvice->measureApplication->measure_name}}</p>
                        <p class="w-150">{{\App\Helpers\NumberFormatter::format($userActionPlanAdvice->costs, 0, true)}} {{\App\Helpers\Hoomdossier::getUnitForColumn('costs')}}</p>
                        <p class="w-150">{{$userActionPlanAdvice->getYear()}}</p>
                    </div>
                @endforeach
            </div>
        @endisset


        @isset($advices['maintenance'][$stepSlug])
            <div class="question-answer-section">
                <div class="measures">
                    <p class="lead w-320">
                        {{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.measures.maintenance.title')}}
                    </p>
                    <p class="lead w-150">
                        {{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.measures.maintenance.costs')}}
                    </p>
                    <p class="lead w-150">
                        {{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.measures.maintenance.year')}}
                    </p>
                </div>
                @foreach($advices['maintenance'][$stepSlug] as $userActionPlanAdvice)
                    <div class="question-answer">
                        <p class="w-320">{{$userActionPlanAdvice->measureApplication->measure_name}}</p>
                        <p class="w-150">{{\App\Helpers\NumberFormatter::format($userActionPlanAdvice->costs, 0, true)}} {{\App\Helpers\Hoomdossier::getUnitForColumn('costs')}}</p>
                        <p class="w-150">{{$userActionPlanAdvice->getYear()}}</p>
                    </div>
                @endforeach
            </div>
        @endisset

        <div class="question-answer-section">
            <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.comments')}}</p>
            @if(array_key_exists($stepSlug, $commentsByStep))
                @foreach($commentsByStep[$stepSlug] as $inputSourceName => $commentsCategorizedUnderColumn)
                    {{-- The column can be a category, this will be the case when the comment is stored under a catergory --}}
                    @foreach($commentsCategorizedUnderColumn as $columnOrCategory => $comment)
                        <div class="question-answer">
                            @if(is_array($comment))
                                @foreach($comment as $column => $c)
                                    <p class="w-320">{{$inputSourceName}} ({{$columnOrCategory}})</p>
                                    <p>{{$c}}</p>
                                @endforeach
                            @else
                                <p class="w-320">{{$inputSourceName}}</p>
                                <p>{{$comment}}</p>
                            @endif
                        </div>
                    @endforeach
                @endforeach
            @endif
        </div>
    </div>
@endcomponent