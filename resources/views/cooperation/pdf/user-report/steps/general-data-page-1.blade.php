@component('cooperation.pdf.components.new-page')
    <div class="container">
        <div class="question-answer-section">
            <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.address-info.title')}}</p>
            <div class="question-answer">
                <p class="w-380">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.address-info.name')}}</p>
                <p>{{$user->getFullName()}}</p>
            </div>
            <div class="question-answer">
                <p class="w-380">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.address-info.address')}}</p>
                <p>{{$building->street}} {{$building->number}} {{$building->extension}}</p>
            </div>
            <div class="question-answer">
                <p class="w-380">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.address-info.zip-code-city')}}</p>
                <p>{{$building->postal_code}} {{$building->city}}</p>
            </div>
        </div>

        @foreach (\Illuminate\Support\Arr::only($reportData[$stepShort], ['building-characteristics', 'current-state']) as $subStepShort => $dataForSubStep)
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

            @if(isset($commentsByStep[$stepShort][$subStepShort]) && !\App\Helpers\Arr::isWholeArrayEmpty($commentsByStep[$stepShort][$subStepShort]))
                @include('cooperation.pdf.user-report.parts.measure-page.comments', [
                    'title' => __('pdf/user-report.general-data.comment'),
                    'comments' => $commentsByStep[$stepShort][$subStepShort],
                ])
            @endif
        @endforeach

    </div>
@endcomponent



@component('cooperation.pdf.components.new-page')
    <div class="container">

        @foreach (\Illuminate\Support\Arr::only($reportData[$stepShort], ['usage', 'interest']) as $subStepShort => $dataForSubStep)
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

            @if(isset($commentsByStep[$stepShort][$subStepShort]) && !\App\Helpers\Arr::isWholeArrayEmpty($commentsByStep[$stepShort][$subStepShort]))
                @include('cooperation.pdf.user-report.parts.measure-page.comments', [
                    'title' => __('pdf/user-report.general-data.comment'),
                    'comments' => $commentsByStep[$stepShort][$subStepShort],
                ])
            @endif
        @endforeach

    </div>
@endcomponent



