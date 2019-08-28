@component('cooperation.pdf.components.new-page')
    <div class="container">

        {{--            <img class="width: 50px; height: 50px;" src="{{public_path('images/'.$step.'.png')}}" alt=""><h2>{{$step}}</h2>--}}
        <div class="step-intro">
            {{--                <img src="{{asset('images/'.$step.'.png')}}" alt="">--}}
            <h2>{{\App\Models\Step::whereSlug($step)->first()->name}}</h2>
        </div>

        <div class="question-answer-section">
            <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.filled-in-data')}}</p>
            <?php
                $calculationsForStep = $data['calculation'] ?? [];
                unset($data['calculation']);
            ?>
            @foreach (\Illuminate\Support\Arr::dot($data) as $translationKey => $value)
                <?php
                    $translationForAnswer = $reportTranslations[$step.'.'.$translationKey];
                ?>
                <div class="question-answer">
{{--                    <p class="w-300">{{$translationForAnswer}}</p>--}}
                    <p class="w-300">{{$translationKey}}</p>
                    <p>{{$value}} {{\App\Helpers\Hoomdossier::getUnitForColumn($translationKey)}}</p>
                </div>
            @endforeach
        </div>


        <div class="question-answer-section">
            <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.indicative-costs-and-benefits-for-measure')}}</p>
            @foreach($calculationsForStep as $calculationType => $calculationResult)
                <div class="question-answer">
                    @if(!empty($calculationResult) && !is_array($calculationResult))
                        <?php
                            $translationForAnswer = $reportTranslations[$step.'.calculation.'.$calculationType];
                        ?>
                        <p class="w-300">{{$translationForAnswer}}</p>
                        <p>{{\App\Helpers\NumberFormatter::round($calculationResult)}} {{\App\Helpers\Hoomdossier::getUnitForColumn($calculationType)}}</p>
                    @endif
                </div>
            @endforeach

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