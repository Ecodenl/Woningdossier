@component('cooperation.pdf.components.new-page')
<div class="container">

    @include('cooperation.pdf.user-report.parts.measure-page.step-intro')

    <div class="question-answer-section">
        <p class="lead">{{$title ?? __('pdf/user-report.measure-pages.filled-in-data')}}</p>
        <table class="full-width">
            <tbody>
            <?php
            $element = \App\Models\Element::findByShort('crawlspace');
            // best bool comparison ever
            $hasNoCrawlspace = $dataForSubStep["element.{$element->id}.extra.has_crawlspace"] === "Nee";
            $hasNoCrawlspaceAccess = $dataForSubStep["element.{$element->id}.extra.access"] === "Nee";
            ?>
            @foreach ($dataForSubStep as $translationKey => $value)
                <?php
                $translationForAnswer = $reportTranslations[$stepShort . '.' . $subStepShort . '.' . $translationKey];
                $tableIsNotUserInterest = !\App\Helpers\Hoomdossier::columnContains($translationKey, 'considerables');
                $isNotCalculation = !\App\Helpers\Hoomdossier::columnContains($translationKey, 'calculation');

                $doesAnswerContainUnit = stripos($value, 'm2') !== false;
                ?>
                {{--we only want the interest on the interest page--}}
                @if($isNotCalculation)
                    <tr class="h-20">
                        <td class="w-380">{{$translationForAnswer}}</td>
                        <td>{{$value}} {{$doesAnswerContainUnit ?'': \App\Helpers\Hoomdossier::getUnitForColumn($translationKey)}}</td>
                    </tr>
                @endif

                {{-- just like in the tool itself, no crawlspace no other questions --}}
                @if($hasNoCrawlspace && $translationKey === "element.{$element->id}.extra.has_crawlspace" )
                    @break
                @endif
            @endforeach
            </tbody>
        </table>
        @if($hasNoCrawlspace)
            <p style="color: darkgray">{{ \App\Helpers\Translation::translate('floor-insulation.has-crawlspace.no-crawlspace.title') }}</p>
        @endif
        {{-- this messages is only shown when crawlspace is present but not accessible  --}}
        @if($hasNoCrawlspaceAccess && !$hasNoCrawlspace)
            <p style="color: darkgray">{{\App\Helpers\Translation::translate('floor-insulation.crawlspace-access.no-access.title')}}</p>
        @endif
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