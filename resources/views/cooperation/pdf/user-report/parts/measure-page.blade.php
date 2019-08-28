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
                    <p class="w-300">{{$translationForAnswer}}</p>
                    <p>{{$value}}</p>
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
                        <p>{{\App\Helpers\NumberFormatter::round($calculationResult)}}</p>
                    @endif
                </div>
            @endforeach

        </div>

        <div class="question-answer-section">
            <div class="measures">
                <p class="lead w-300">
                    {{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.measures.title')}}
                </p>
                <p class="lead w-150">
                    {{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.measures.costs')}}
                </p>
                <p class="lead w-150">
                    {{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.measures.year')}}
                </p>
            </div>

            @foreach($userActionPlanAdvicesWithMaintenance as $userActionPlanAdvice)
                <div class="question-answer">
                    <p class="w-300">{{$userActionPlanAdvice->measureApplication->measure_name}}</p>
                    <p class="w-150">{{\App\Helpers\NumberFormatter::round($userActionPlanAdvice->costs)}}</p>
                    <p class="w-150">{{$userActionPlanAdvice->getYear()}}</p>
                </div>
            @endforeach
        </div>

        <div class="question-answer-section">
            <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.comments')}}</p>
            @if(isset($commentsByStep[$step]))
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
                        @endforeach
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endcomponent