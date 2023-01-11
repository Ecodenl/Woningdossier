@component('cooperation.pdf.components.new-page')
    <div class="container">

        <?php

        // we have to pull apart the interests and filled in data
        // because we have to show the interests with a different title
        $ventilationData = \App\Helpers\Arr::undot($dataForSubStep);

        $warnings = \App\Helpers\Cooperation\Tool\VentilationHelper::getWarningForValues();
        ?>

        @include('cooperation.pdf.user-report.parts.measure-page.step-intro')

        {{--undot it so we can only get the building ventilations--}}
        {{--and dot is back again so we can use the include--}}

        <div class="question-answer-section">
            <p class="lead">{{$title ?? __('pdf/user-report.measure-pages.filled-in-data')}}</p>
            @foreach ( \Illuminate\Support\Arr::dot($ventilationData['building_ventilations'], 'building_ventilations.') as $translationKey => $value)

                <?php
                $translationForAnswer = $reportTranslations[$stepShort . '.' . $subStepShort . '.' . $translationKey];
                // so we can loop through them and get the warnings.
                $ventilationAnswers = [];
                if (!is_null($value)) {
                    $ventilationAnswers = explode(', ', $value);
                }
                ?>

                @if(!empty($ventilationAnswers))

                    {{--we only want the interest on the interest page--}}
                    <p>{{$translationForAnswer}}</p>
                    <ul>
                        @foreach($ventilationAnswers as $ventilationAnswer)
                            <li>{{$ventilationAnswer}}</li>
                        @endforeach
                    </ul>

                    @foreach($ventilationAnswers as $ventilationAnswer)
                        <?php
                        // here we will have to do some ugly stuff, we will get the selected option key from the given answer

                        // these are the options for an question, the $ventilatioAnswer holds the translation op that option
                        // so we flip the array so we can get the option key by the translation of it.
                        $optionsForAnswer = array_flip(
                            \App\Helpers\ToolHelper::getLegacyContentStructure("ventilation.-.{$translationKey}")['options']
                        );

                        $ventilationValue = $optionsForAnswer[$ventilationAnswer] ?? null;

                        // with the value we can get the warning.
                        ?>
                        @if(array_key_exists($ventilationValue, $warnings))
                            <p style="color: darkgray">{{$warnings[$ventilationValue]}}</p>
                        @endif
                    @endforeach
                    <br>
                @endif
            @endforeach
        </div>

            <div class="question-answer-section">
                <p class="lead">@lang('pdf/user-report.ventilation.points-of-improvement')</p>
                <table class="full-width">
                    <tbody>
                    @php
                        $ventilationStepId = App\Models\Step::findByShort($stepShort)->id;
                    @endphp
                    @foreach($userActionPlanAdvices->where('step_id', $ventilationStepId) as $userActionPlanAdvice)
                            <tr class="h-20">
                                <td class="w-380">{{$userActionPlanAdvice->userActionPlanAdvisable->measure_name}}</td>
                            </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

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