@component('cooperation.pdf.user-report.components.new-page', ['id' => 'small-measures'])
    <h2>
        @lang('pdf/user-report.pages.small-measures.title')
    </h2>
    <p>
        @lang('pdf/user-report.pages.small-measures.text')
    </p>

    <div class="group">
        @foreach($smallMeasureAdvices as $category => $advices)
            @foreach($advices as $advice)
                <div class="row">
                    <div class="col-12">
                        <h5>
                            {{ $advice->userActionPlanAdvisable->name }}
                        </h5>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <p>
                            {!! nl2br($advice->userActionPlanAdvisable->info) !!}
                        </p>
                    </div>
                </div>
                <div class="row py-3">
                    <div class="col-3">
                        <h4>
                            @lang('pdf/user-report.pages.action-plan.advices.cost-indication')
                        </h4>
                    </div>
                    <div class="col-3">
                        <p>
                            {{ $advice->costs }}
                        </p>
                    </div>
                    <div class="col-3">
                        <h4>
                            @lang('pdf/user-report.pages.action-plan.advices.savings')
                        </h4>
                    </div>
                    <div class="col-3">
                        <p>
                            {{ $advice->savings_money }}
                        </p>
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>
@endcomponent