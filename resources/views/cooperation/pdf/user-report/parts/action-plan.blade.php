@component('cooperation.pdf.components.new-page')
    <div id="action-plan" class="container">
        <h2>
            @lang('pdf/user-report.action-plan.title')
        </h2>
        <p>
            @lang('pdf/user-report.action-plan.text')
        </p>

        <div class="group">
            <h4>
                @lang('pdf/user-report.action-plan.usage.current')
            </h4>

            <div class="row">
                <div class="col-2">
                    <p>
                        {{ Str::ucfirst(__('general.unit.gas.title')) }}
                    </p>
                </div>
                <div class="col-9">
                    <p>

                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-2">
                    <p>
                        {{ Str::ucfirst(__('general.unit.electric.title')) }}
                    </p>
                </div>
                <div class="col-10">
                    <p>

                    </p>
                </div>
            </div>
        </div>

        <div class="group">
            <h4>
                @lang('pdf/user-report.action-plan.usage.kengetallen')
            </h4>

            <div class="row">
                <div class="col-2">
                    <p>
                        {{ Str::ucfirst(__('general.unit.gas.title')) }}
                    </p>
                </div>
                <div class="col-10">
                    <p>
                        {!! number_format(Kengetallen::EURO_SAVINGS_GAS, 2, ',', '.') . ' € / m<sup>3</sup>' !!}
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-2">
                    <p>
                        {{ Str::ucfirst(__('general.unit.electric.title')) }}
                    </p>
                </div>
                <div class="col-10">
                    <p>
                        {!! number_format(Kengetallen::EURO_SAVINGS_ELECTRICITY, 2, ',', '.') . ' € / m<sup>3</sup>' !!}
                    </p>
                </div>
            </div>
        </div>

        @foreach($categorizedAdvices as $category => $advices)
            @if($advices->isNotEmpty())
                @php $showDetails = $category !== \App\Services\UserActionPlanAdviceService::CATEGORY_COMPLETE; @endphp
                <div class="group">
                    <h3>
                        @lang("pdf/user-report.action-plan.categories.{$category}")
                    </h3>

                    @if($showDetails)
                        <div class="row">
                            <div class="col-4">
                                <h4>
                                    @lang('pdf/user-report.action-plan.advices.measure')
                                </h4>
                            </div>
                            <div class="col-3 text-center">
                                <h4>
                                    @lang('pdf/user-report.action-plan.advices.cost-indication')
                                </h4>
                            </div>
                            <div class="col-3 text-center">
                                <h4>
                                    @lang('pdf/user-report.action-plan.advices.savings')
                                </h4>
                            </div>
                        </div>
                    @endif

                    @foreach($advices as $advice)
                        <div class="row">
                            <div class="col-4">
                                <p>
                                    {{ $advice->userActionPlanAdvisable->name }}
                                </p>
                            </div>
                            @if($showDetails)
                                {{-- Mapped after retrieval --}}
                                <div class="col-3 text-center">
                                    <p>
                                        {{ $advice->costs }}
                                    </p>
                                </div>
                                <div class="col-3 text-center">
                                    <p>
                                        {{ $advice->savings_money }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        @endforeach

        @if($adviceComments->isNotEmpty())
            <div class="group">
                @foreach($adviceComments as $comment)
                    @include('cooperation.pdf.user-report.parts.comment')
                @endforeach
            </div>
        @endif
    </div>
@endcomponent