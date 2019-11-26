@foreach ($reportData['general-data'] as $subStepShort => $dataForSubStep)
    <?php
    ?>
    <div class="question-answer-section">
        <p class="lead">
            {{\App\Models\Step::findByShort($subStepShort)->name}}
        </p>

        <table class="full-width">
            <tbody>
            @foreach ($dataForSubStep as $translationKey => $value)
                <?php
                $translationForAnswer = $reportTranslations['general-data.' . $subStepShort . '.' . $translationKey];

                $tableIsNotUserInterest = !\App\Helpers\Hoomdossier::columnContains($translationKey, 'user_interests');

                $doesAnswerContainUnit = stripos($value, 'm2') !== false;
                ?>
                @if($tableIsNotUserInterest || $subStepShort == 'interest')
                    <tr class="h-20">
                        <td class="w-380">{{$translationForAnswer}}</td>
                        <td>{{$value}} {{$doesAnswerContainUnit ?'': \App\Helpers\Hoomdossier::getUnitForColumn($translationKey)}}</td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>
@endforeach
