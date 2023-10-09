@component('cooperation.pdf.user-report.components.new-page', ['id' => 'action-plan'])
    <h1 class="mb-2">
        @lang('pdf/user-report.pages.action-plan.title')
    </h1>
    <p>
        @lang('pdf/user-report.pages.action-plan.text')
    </p>

    <div class="group">
        @php $stepShort = $scanShort === \App\Models\Scan::LITE ? 'usage-lite-scan' : 'usage-quick-scan'; @endphp
        <h3>
            @lang('pdf/user-report.pages.action-plan.usage.current')
        </h3>

        <div class="row">
            <div class="col-2">
                <p>
                    {{ Str::ucfirst(__('general.unit.gas.title')) }}
                </p>
            </div>
            <div class="col-9">
                <p>
{{--                    {!! $simpleDump[$stepShort]['question_amount-gas'] !!}--}}
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
{{--                    {!! $simpleDump[$stepShort]['question_amount-electricity'] !!}--}}
                </p>
            </div>
        </div>
    </div>

    <div class="group">
        <h3>
            @lang('pdf/user-report.pages.action-plan.usage.kengetallen')
        </h3>

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
                    {!! number_format(Kengetallen::EURO_SAVINGS_ELECTRICITY, 2, ',', '.') . ' € / kWh' !!}
                </p>
            </div>
        </div>
    </div>

    @foreach($categorizedAdvices as $category => $advices)
        @if($advices->isNotEmpty())
            @php $showDetails = $category !== \App\Services\UserActionPlanAdviceService::CATEGORY_COMPLETE; @endphp
            <div class="group">
                <h3>
                    @lang("pdf/user-report.pages.action-plan.categories.{$category}")
                </h3>

                @if($showDetails)
                    <div class="row">
                        <div class="col-6">
                            <h4>
                                @lang('pdf/user-report.pages.action-plan.advices.measure')
                            </h4>
                        </div>
                        <div class="col-3 text-center">
                            <h4>
                                @lang('pdf/user-report.pages.action-plan.advices.cost-indication')
                            </h4>
                        </div>
                        <div class="col-3 text-center">
                            <h4>
                                @lang('pdf/user-report.pages.action-plan.advices.savings')
                            </h4>
                        </div>
                    </div>
                @endif

                @foreach($advices as $advice)
                    <div class="row">
                        <div class="col-6">
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

                @if($category === \App\Services\UserActionPlanAdviceService::CATEGORY_TO_DO && $alerts->isNotEmpty())
                    <div class="group">
                        <div class="row">
                            <p>
                                @lang('pdf/user-report.alerts.text')
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    @endforeach

    @if($adviceComments->isNotEmpty())
        <div class="group">
            <h4>
                @lang('pdf/user-report.pages.action-plan.comment')
            </h4>
            @foreach($adviceComments as $comment)
                <div class="py-2">
                    @include('cooperation.pdf.user-report.parts.comment', [
                        'label' => $comment->inputSource->name,
                        'comment' => $comment->comment,
                    ])
                </div>
            @endforeach
        </div>
    @endif
@endcomponent