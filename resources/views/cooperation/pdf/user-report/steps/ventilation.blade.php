@component('cooperation.pdf.components.new-page')
    <div class="container">

        <?php

        // we have to pull apart the interests and filled in data
        // because we have to show the interests with a different title
        $ventilationData = \App\Helpers\Arr::arrayUndot($dataForSubStep);

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
                            \App\Helpers\ToolHelper::getContentStructure("ventilation.-.{$translationKey}")['options']
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

        @include('cooperation.pdf.user-report.parts.measure-page.filled-in-data', [
            'title' => __('pdf/user-report.ventilation.points-of-improvement'),
            'dataForSubStep' => \Illuminate\Support\Arr::dot(
                $ventilationData['user_interests'],
                'user_interests.'
            )
        ])

        @include('cooperation.pdf.user-report.parts.measure-page.insulation-advice')

        @include('cooperation.pdf.user-report.parts.measure-page.indicative-costs-and-measures')

        @include('cooperation.pdf.user-report.parts.measure-page.advices')

        @include('cooperation.pdf.user-report.parts.measure-page.comments')
    </div>
@endcomponent