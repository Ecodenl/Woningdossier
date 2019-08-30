@component('cooperation.pdf.components.new-page')
    <div class="container">

        <div class="step-intro">
            <img src="{{public_path('images/icons/'.$step.'.png')}}" alt="">
            {{--<img src="{{asset('images/icons/'.$step.'.png')}}" alt="">--}}
            <h2>{{\App\Models\Step::whereSlug($step)->first()->name}}</h2>
        </div>

        <div class="question-answer-section">
            <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.filled-in-data')}}</p>
            <?php
                $calculationsForStep = $data['calculation'] ?? [];
                unset($data['calculation']);
            ?>

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
                        <td>{{\App\Helpers\NumberFormatter::round($calculationResult)}} {{\App\Helpers\Hoomdossier::getUnitForColumn($calculationType)}}</td>
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
    </div>
@endcomponent